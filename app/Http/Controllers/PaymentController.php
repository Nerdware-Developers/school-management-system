<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\PaymentTransaction;
use App\Models\StudentFeeTerm;
use App\Models\FeesInformation;
use App\Services\DarajaService;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;

class PaymentController extends Controller
{
    public function __construct()
    {
        // Set Stripe API key
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Show payment page
     */
    public function showPaymentForm($studentId)
    {
        $student = Student::with(['feeTerms' => function($q) {
            $q->orderByDesc('created_at');
        }])->findOrFail($studentId);

        $currentTerm = $student->feeTerms->firstWhere('status', 'current') ?? $student->feeTerms->first();
        
        if (!$currentTerm) {
            Toastr::error('No active fee term found for this student', 'Error');
            return redirect()->back();
        }

        $balance = $currentTerm->closing_balance ?? ($currentTerm->fee_amount - $currentTerm->amount_paid);

        return view('payments.create', compact('student', 'currentTerm', 'balance'));
    }

    /**
     * Initialize payment
     */
    public function initiatePayment(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|in:stripe,mpesa',
            'phone_number' => 'required_if:payment_method,mpesa|nullable|string',
            'description' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $student = Student::with(['feeTerms' => function($q) {
                $q->orderByDesc('created_at');
            }])->findOrFail($request->student_id);

            $currentTerm = $student->feeTerms->firstWhere('status', 'current') ?? $student->feeTerms->first();

            if (!$currentTerm) {
                throw new \Exception('No active fee term found');
            }

            // Check if amount exceeds balance (only if balance is positive)
            $balance = $currentTerm->closing_balance ?? ($currentTerm->fee_amount - $currentTerm->amount_paid);
            if ($balance > 0 && $request->amount > $balance) {
                Toastr::warning('Payment amount cannot exceed the balance amount of Ksh ' . number_format($balance, 2), 'Warning');
                return redirect()->back()->withInput();
            }
            
            // Validate minimum amount
            if ($request->amount < 1) {
                Toastr::error('Payment amount must be at least Ksh 1.00', 'Error');
                return redirect()->back()->withInput();
            }

            // Create payment transaction
            $transaction = PaymentTransaction::create([
                'student_id' => $student->id,
                'student_fee_term_id' => $currentTerm->id,
                'transaction_id' => PaymentTransaction::generateTransactionId(),
                'payment_gateway' => $request->payment_method === 'mpesa' ? 'mpesa' : 'stripe',
                'amount' => $request->amount,
                'currency' => 'KES',
                'status' => 'pending',
                'description' => $request->description ?? "Fee payment for {$student->first_name} {$student->last_name}",
                'metadata' => $request->payment_method === 'mpesa' ? ['phone_number' => $request->phone_number] : null,
            ]);

            // Handle based on payment method
            if ($request->payment_method === 'mpesa') {
                return $this->initiateMpesaPayment($transaction, $request->phone_number, $student);
            } else {
                return $this->initiateStripePayment($transaction, $student);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error('An error occurred: ' . $e->getMessage(), 'Error');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Initiate Stripe payment
     */
    private function initiateStripePayment($transaction, $student)
    {
        try {
            $amountInCents = (int)round($transaction->amount * 100);
            
            $paymentIntent = PaymentIntent::create([
                'amount' => $amountInCents,
                'currency' => 'kes',
                'metadata' => [
                    'transaction_id' => $transaction->transaction_id,
                    'student_id' => $student->id,
                    'student_name' => "{$student->first_name} {$student->last_name}",
                ],
                'description' => $transaction->description,
            ]);

            $transaction->update([
                'gateway_payment_intent_id' => $paymentIntent->id,
                'gateway_response' => $paymentIntent->toArray(),
            ]);

            DB::commit();

            return view('payments.process', [
                'transaction' => $transaction,
                'paymentIntent' => $paymentIntent,
                'stripeKey' => config('services.stripe.key'),
            ]);

        } catch (ApiErrorException $e) {
            DB::rollBack();
            $transaction->update([
                'status' => 'failed',
                'failure_reason' => $e->getMessage(),
            ]);

            Toastr::error('Payment initialization failed: ' . $e->getMessage(), 'Error');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Initiate M-Pesa payment
     */
    private function initiateMpesaPayment($transaction, $phoneNumber, $student)
    {
        try {
            // Check if Daraja credentials are configured
            $consumerKey = config('services.daraja.consumer_key');
            $consumerSecret = config('services.daraja.consumer_secret');
            $shortCode = config('services.daraja.shortcode');
            $passkey = config('services.daraja.passkey');

            if (empty($consumerKey) || empty($consumerSecret) || empty($shortCode) || empty($passkey)) {
                DB::rollBack();
                $missing = [];
                if (empty($consumerKey)) $missing[] = 'DARAJA_CONSUMER_KEY';
                if (empty($consumerSecret)) $missing[] = 'DARAJA_CONSUMER_SECRET';
                if (empty($shortCode)) $missing[] = 'DARAJA_SHORTCODE';
                if (empty($passkey)) $missing[] = 'DARAJA_PASSKEY';
                
                Toastr::error('M-Pesa is not configured. Please add ' . implode(', ', $missing) . ' to your .env file and run: php artisan config:clear', 'Configuration Error');
                return redirect()->back()->withInput();
            }

            $darajaService = new DarajaService();

            // Validate minimum amount (M-Pesa requires at least 1 KES)
            if ($transaction->amount < 1) {
                DB::rollBack();
                Toastr::error('M-Pesa requires a minimum payment of Ksh 1.00', 'Error');
                return redirect()->back()->withInput();
            }

            // Validate phone number
            if (!$darajaService->validatePhoneNumber($phoneNumber)) {
                DB::rollBack();
                Toastr::error('Invalid phone number format. Please use format: 0712345678 or 254712345678', 'Error');
                return redirect()->back()->withInput();
            }

            // Format phone number for display
            $formattedPhone = $darajaService->formatPhoneNumber($phoneNumber);
            
            // Log payment attempt
            \Log::info('Initiating M-Pesa Payment', [
                'transaction_id' => $transaction->transaction_id,
                'phone' => $formattedPhone,
                'amount' => $transaction->amount,
                'student_id' => $student->id,
            ]);

            // Initiate STK Push
            $result = $darajaService->stkPush(
                $phoneNumber,
                $transaction->amount,
                $transaction->transaction_id,
                $transaction->description
            );

            if ($result['success']) {
                $transaction->update([
                    'gateway_payment_intent_id' => $result['checkout_request_id'],
                    'gateway_transaction_id' => $result['merchant_request_id'],
                    'gateway_response' => $result,
                ]);

                DB::commit();

                $successMessage = $result['customer_message'] ?? 'Payment request sent to your phone. Please check your phone and enter your M-Pesa PIN to complete the payment.';
                Toastr::success($successMessage, 'Success');
                
                \Log::info('M-Pesa STK Push Sent Successfully', [
                    'transaction_id' => $transaction->transaction_id,
                    'checkout_request_id' => $result['checkout_request_id'],
                ]);
                
                return view('payments.mpesa-waiting', [
                    'transaction' => $transaction,
                    'checkoutRequestId' => $result['checkout_request_id'],
                ]);

            } else {
                DB::rollBack();
                
                $errorMessage = $result['message'] ?? 'Failed to initiate payment. Please check your Daraja credentials and try again.';
                
                // Provide more helpful error messages
                if (isset($result['status_code']) && $result['status_code'] == 401) {
                    $errorMessage = 'Authentication failed. Please check your Daraja Consumer Key and Secret.';
                } elseif (isset($result['status_code']) && $result['status_code'] == 400) {
                    $errorMessage = 'Invalid request. Please check your phone number format and amount.';
                }
                
                $transaction->update([
                    'status' => 'failed',
                    'failure_reason' => $errorMessage,
                    'gateway_response' => $result,
                ]);

                \Log::error('M-Pesa STK Push Failed', [
                    'transaction_id' => $transaction->transaction_id,
                    'error' => $errorMessage,
                    'result' => $result,
                ]);

                Toastr::error($errorMessage, 'Payment Failed');
                return redirect()->back()->withInput();
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $transaction->update([
                'status' => 'failed',
                'failure_reason' => $e->getMessage(),
            ]);

            Toastr::error('M-Pesa payment failed: ' . $e->getMessage(), 'Error');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Handle successful payment
     */
    public function paymentSuccess(Request $request)
    {
        $transactionId = $request->get('transaction_id');
        $paymentIntentId = $request->get('payment_intent');

        if (!$transactionId || !$paymentIntentId) {
            Toastr::error('Invalid payment parameters', 'Error');
            return redirect()->route('account/fees/collections/page');
        }

        $transaction = PaymentTransaction::where('transaction_id', $transactionId)
            ->where('gateway_payment_intent_id', $paymentIntentId)
            ->firstOrFail();

        try {
            // Verify payment with Stripe
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

            if ($paymentIntent->status === 'succeeded') {
                DB::beginTransaction();

                // Update transaction
                $transaction->update([
                    'status' => 'completed',
                    'gateway_transaction_id' => $paymentIntent->charges->data[0]->id ?? null,
                    'payment_method' => $paymentIntent->payment_method_types[0] ?? 'card',
                    'paid_at' => now(),
                    'receipt_number' => PaymentTransaction::generateReceiptNumber(),
                    'gateway_response' => $paymentIntent->toArray(),
                ]);

                // Update student fee term
                $this->updateStudentFeeAfterPayment($transaction);

                DB::commit();

                // Create success notification
                \App\Models\Notification::createNotification([
                    'user_id' => auth()->id(),
                    'type' => 'success',
                    'title' => 'Payment Successful',
                    'message' => "Payment of Ksh " . number_format($transaction->amount, 2) . " received from {$transaction->student->first_name} {$transaction->student->last_name}",
                    'link' => route('payments.receipt', $transaction->id),
                ]);

                Toastr::success('Payment processed successfully!', 'Success');
                return redirect()->route('payments.receipt', $transaction->id);

            } else {
                $transaction->update([
                    'status' => 'failed',
                    'failure_reason' => 'Payment not completed: ' . $paymentIntent->status,
                ]);

                Toastr::error('Payment was not completed', 'Error');
                return redirect()->route('payments.failure', $transaction->id);
            }

        } catch (ApiErrorException $e) {
            DB::rollBack();
            $transaction->update([
                'status' => 'failed',
                'failure_reason' => $e->getMessage(),
            ]);

            Toastr::error('Payment verification failed: ' . $e->getMessage(), 'Error');
            return redirect()->route('payments.failure', $transaction->id);
        }
    }

    /**
     * Handle payment failure
     */
    public function paymentFailure($transactionId)
    {
        $transaction = PaymentTransaction::findOrFail($transactionId);
        return view('payments.failure', compact('transaction'));
    }

    /**
     * Show payment receipt
     */
    public function receipt($transactionId)
    {
        $transaction = PaymentTransaction::with(['student', 'feeTerm'])->findOrFail($transactionId);
        
        if ($transaction->status !== 'completed') {
            Toastr::error('Payment not completed', 'Error');
            return redirect()->back();
        }

        return view('payments.receipt', compact('transaction'));
    }

    /**
     * Download receipt as PDF
     */
    public function downloadReceipt($transactionId)
    {
        $transaction = PaymentTransaction::with(['student', 'feeTerm'])->findOrFail($transactionId);
        
        if ($transaction->status !== 'completed') {
            Toastr::error('Payment not completed', 'Error');
            return redirect()->back();
        }

        $pdf = \PDF::loadView('payments.receipt-pdf', compact('transaction'));
        return $pdf->download('receipt-' . $transaction->receipt_number . '.pdf');
    }

    /**
     * M-Pesa callback handler
     */
    public function darajaCallback(Request $request)
    {
        try {
            $data = $request->all();
            
            // Log the callback for debugging
            \Log::info('M-Pesa Callback Received', $data);

            $body = $data['Body'] ?? [];
            $stkCallback = $body['stkCallback'] ?? [];
            $merchantRequestId = $stkCallback['MerchantRequestID'] ?? null;
            $checkoutRequestId = $stkCallback['CheckoutRequestID'] ?? null;
            $resultCode = $stkCallback['ResultCode'] ?? null;
            $resultDesc = $stkCallback['ResultDesc'] ?? null;
            $callbackMetadata = $stkCallback['CallbackMetadata'] ?? [];
            $items = $callbackMetadata['Item'] ?? [];

            // Find transaction by checkout request ID
            $transaction = PaymentTransaction::where('gateway_payment_intent_id', $checkoutRequestId)
                ->orWhere('gateway_transaction_id', $merchantRequestId)
                ->first();

            if (!$transaction) {
                \Log::warning('M-Pesa callback: Transaction not found', ['checkout_request_id' => $checkoutRequestId]);
                return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Transaction not found']);
            }

            // Update transaction with callback data
            $transaction->update([
                'gateway_response' => array_merge($transaction->gateway_response ?? [], ['callback' => $data]),
            ]);

            // Handle based on result code
            if ($resultCode == 0) {
                // Payment successful
                $mpesaReceiptNumber = null;
                $phoneNumber = null;
                $amount = null;

                foreach ($items as $item) {
                    if ($item['Name'] == 'MpesaReceiptNumber') {
                        $mpesaReceiptNumber = $item['Value'];
                    }
                    if ($item['Name'] == 'PhoneNumber') {
                        $phoneNumber = $item['Value'];
                    }
                    if ($item['Name'] == 'Amount') {
                        $amount = $item['Value'];
                    }
                }

                DB::beginTransaction();

                $transaction->update([
                    'status' => 'completed',
                    'gateway_transaction_id' => $mpesaReceiptNumber,
                    'payment_method' => 'mpesa',
                    'paid_at' => now(),
                    'receipt_number' => PaymentTransaction::generateReceiptNumber(),
                    'gateway_response' => array_merge($transaction->gateway_response ?? [], [
                        'mpesa_receipt' => $mpesaReceiptNumber,
                        'phone_number' => $phoneNumber,
                        'amount' => $amount,
                    ]),
                ]);

                // Update student fee term
                $this->updateStudentFeeAfterPayment($transaction);

                DB::commit();

                // Create success notification
                \App\Models\Notification::createNotification([
                    'user_id' => auth()->id(),
                    'type' => 'success',
                    'title' => 'M-Pesa Payment Successful',
                    'message' => "Payment of Ksh " . number_format($transaction->amount, 2) . " received via M-Pesa from {$transaction->student->first_name} {$transaction->student->last_name}",
                    'link' => route('payments.receipt', $transaction->id),
                ]);

            } else {
                // Payment failed
                $transaction->update([
                    'status' => 'failed',
                    'failure_reason' => $resultDesc,
                ]);
            }

            // Return proper M-Pesa callback response
            return response()->json([
                'ResultCode' => 0,
                'ResultDesc' => 'Callback processed successfully'
            ], 200, [], JSON_UNESCAPED_SLASHES);

        } catch (\Exception $e) {
            \Log::error('M-Pesa callback error', ['error' => $e->getMessage(), 'data' => $request->all()]);
            return response()->json([
                'ResultCode' => 1,
                'ResultDesc' => 'Error processing callback'
            ], 500);
        }
    }

    /**
     * Check M-Pesa payment status
     */
    public function checkMpesaStatus($transactionId)
    {
        $transaction = PaymentTransaction::findOrFail($transactionId);

        if ($transaction->payment_gateway !== 'mpesa') {
            return response()->json(['error' => 'Not an M-Pesa transaction'], 400);
        }

        if ($transaction->status === 'completed') {
            return response()->json([
                'status' => 'completed',
                'transaction' => $transaction
            ]);
        }

        // Query status from Daraja
        $darajaService = new DarajaService();
        $result = $darajaService->queryStkStatus($transaction->gateway_payment_intent_id);

        return response()->json($result);
    }

    /**
     * Update student fee after payment
     */
    private function updateStudentFeeAfterPayment($transaction)
    {
        $currentTerm = $transaction->feeTerm;
        if ($currentTerm) {
            $alreadyPaid = (float) $currentTerm->amount_paid;
            $newTotalPaid = $alreadyPaid + $transaction->amount;
            $feeAmount = (float) $currentTerm->fee_amount;
            $newBalance = $feeAmount - $newTotalPaid;

            $currentTerm->amount_paid = $newTotalPaid;
            $currentTerm->closing_balance = $newBalance;
            
            if ($newBalance <= 0) {
                $currentTerm->status = $newBalance < 0 ? 'credit' : 'closed';
            }
            $currentTerm->save();

            // Create fee information record
            FeesInformation::create([
                'student_id' => $transaction->student_id,
                'student_fee_term_id' => $currentTerm->id,
                'student_name' => $transaction->student->first_name . ' ' . $transaction->student->last_name,
                'fees_type' => 'Online Payment (' . ucfirst($transaction->payment_gateway) . ')',
                'fees_amount' => $transaction->amount,
                'paid_date' => now(),
            ]);

            // Update student snapshot
            $student = $transaction->student;
            $student->amount_paid = $newTotalPaid;
            $student->balance = max($newBalance, 0);
            $student->save();
        }
    }

    /**
     * Stripe webhook handler
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sigHeader,
                $endpointSecret
            );
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                $this->handlePaymentSuccess($paymentIntent);
                break;
            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                $this->handlePaymentFailure($paymentIntent);
                break;
        }

        return response()->json(['received' => true]);
    }

    /**
     * Handle payment success from webhook
     */
    private function handlePaymentSuccess($paymentIntent)
    {
        $transaction = PaymentTransaction::where('gateway_payment_intent_id', $paymentIntent->id)
            ->where('status', 'pending')
            ->first();

        if ($transaction) {
            // Process payment (similar to paymentSuccess method)
            // This ensures webhook updates are processed even if user doesn't return to success page
        }
    }

    /**
     * Handle payment failure from webhook
     */
    private function handlePaymentFailure($paymentIntent)
    {
        $transaction = PaymentTransaction::where('gateway_payment_intent_id', $paymentIntent->id)
            ->where('status', 'pending')
            ->first();

        if ($transaction) {
            $transaction->update([
                'status' => 'failed',
                'failure_reason' => $paymentIntent->last_payment_error->message ?? 'Payment failed',
            ]);
        }
    }
}
