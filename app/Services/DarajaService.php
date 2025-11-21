<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class DarajaService
{
    private $consumerKey;
    private $consumerSecret;
    private $shortCode;
    private $passkey;
    private $baseUrl;
    private $callbackUrl;

    public function __construct()
    {
        $this->consumerKey = config('services.daraja.consumer_key');
        $this->consumerSecret = config('services.daraja.consumer_secret');
        $this->shortCode = config('services.daraja.shortcode');
        $this->passkey = config('services.daraja.passkey');
        $this->baseUrl = config('services.daraja.base_url', 'https://sandbox.safaricom.co.ke');
        
        // Get callback URL from config, or generate it safely
        $this->callbackUrl = config('services.daraja.callback_url');
        if (empty($this->callbackUrl) && app()->runningInConsole() === false) {
            $this->callbackUrl = url('/payments/daraja/callback');
        } elseif (empty($this->callbackUrl)) {
            $this->callbackUrl = 'http://localhost:8000/payments/daraja/callback';
        }
    }

    /**
     * Get OAuth access token
     */
    public function getAccessToken()
    {
        // Check cache first
        $token = Cache::get('daraja_access_token');
        if ($token) {
            return $token;
        }

        try {
            // Ensure credentials are not null
            if (empty($this->consumerKey) || empty($this->consumerSecret)) {
                Log::error('Daraja OAuth failed: Missing credentials', [
                    'consumer_key_set' => !empty($this->consumerKey),
                    'consumer_secret_set' => !empty($this->consumerSecret),
                ]);
                return null;
            }

            $response = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
                ->get($this->baseUrl . '/oauth/v1/generate?grant_type=client_credentials');

            if ($response->successful()) {
                $data = $response->json();
                $token = $data['access_token'] ?? null;
                
                if ($token) {
                    // Cache token for 55 minutes (tokens expire in 1 hour)
                    Cache::put('daraja_access_token', $token, now()->addMinutes(55));
                    return $token;
                }
            }

            Log::error('Daraja OAuth failed', ['response' => $response->body()]);
            return null;
        } catch (\Exception $e) {
            Log::error('Daraja OAuth exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Generate password for STK Push
     */
    private function generatePassword()
    {
        $timestamp = date('YmdHis');
        $password = base64_encode($this->shortCode . $this->passkey . $timestamp);
        return ['password' => $password, 'timestamp' => $timestamp];
    }

    /**
     * Initiate STK Push payment
     */
    public function stkPush($phoneNumber, $amount, $accountReference, $transactionDesc)
    {
        $accessToken = $this->getAccessToken();
        
        if (!$accessToken) {
            return [
                'success' => false,
                'message' => 'Failed to authenticate with M-Pesa'
            ];
        }

        // Format phone number (remove + and ensure it starts with 254)
        $phoneNumber = $this->formatPhoneNumber($phoneNumber);
        
        // Generate password
        $passwordData = $this->generatePassword();

        try {
            $requestData = [
                'BusinessShortCode' => $this->shortCode,
                'Password' => $passwordData['password'],
                'Timestamp' => $passwordData['timestamp'],
                'TransactionType' => 'CustomerPayBillOnline',
                'Amount' => (int)$amount,
                'PartyA' => $phoneNumber,
                'PartyB' => $this->shortCode,
                'PhoneNumber' => $phoneNumber,
                'CallBackURL' => $this->callbackUrl,
                'AccountReference' => $accountReference,
                'TransactionDesc' => $transactionDesc,
            ];

            // Log the request for debugging
            Log::info('M-Pesa STK Push Request', [
                'phone' => $phoneNumber,
                'amount' => $amount,
                'shortcode' => $this->shortCode,
                'callback_url' => $this->callbackUrl,
            ]);

            $response = Http::withToken($accessToken)
                ->timeout(30)
                ->post($this->baseUrl . '/mpesa/stkpush/v1/processrequest', $requestData);

            $statusCode = $response->status();
            $data = $response->json();
            $responseBody = $response->body();

            // Log the full response
            Log::info('M-Pesa STK Push Response', [
                'status_code' => $statusCode,
                'response' => $data,
                'raw_body' => $responseBody,
            ]);

            if ($response->successful() && isset($data['ResponseCode']) && $data['ResponseCode'] == '0') {
                return [
                    'success' => true,
                    'merchant_request_id' => $data['MerchantRequestID'],
                    'checkout_request_id' => $data['CheckoutRequestID'],
                    'response_code' => $data['ResponseCode'],
                    'customer_message' => $data['CustomerMessage'],
                ];
            }

            // Handle different error scenarios
            $errorMessage = 'Payment request failed';
            if (isset($data['errorMessage'])) {
                $errorMessage = $data['errorMessage'];
            } elseif (isset($data['CustomerMessage'])) {
                $errorMessage = $data['CustomerMessage'];
            } elseif (isset($data['error_description'])) {
                $errorMessage = $data['error_description'];
            } elseif (!$response->successful()) {
                $errorMessage = "HTTP Error {$statusCode}: " . ($responseBody ?: 'No response body');
            }

            Log::error('M-Pesa STK Push Failed', [
                'status_code' => $statusCode,
                'error' => $errorMessage,
                'response' => $data,
            ]);

            return [
                'success' => false,
                'message' => $errorMessage,
                'response' => $data,
                'status_code' => $statusCode,
            ];

        } catch (\Exception $e) {
            Log::error('Daraja STK Push exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Query STK Push status
     */
    public function queryStkStatus($checkoutRequestId)
    {
        $accessToken = $this->getAccessToken();
        
        if (!$accessToken) {
            return ['success' => false, 'message' => 'Failed to authenticate'];
        }

        $passwordData = $this->generatePassword();

        try {
            $response = Http::withToken($accessToken)
                ->post($this->baseUrl . '/mpesa/stkpushquery/v1/query', [
                    'BusinessShortCode' => $this->shortCode,
                    'Password' => $passwordData['password'],
                    'Timestamp' => $passwordData['timestamp'],
                    'CheckoutRequestID' => $checkoutRequestId,
                ]);

            $data = $response->json();

            return [
                'success' => $response->successful(),
                'response_code' => $data['ResponseCode'] ?? null,
                'result_code' => $data['ResultCode'] ?? null,
                'result_desc' => $data['ResultDesc'] ?? null,
                'data' => $data
            ];

        } catch (\Exception $e) {
            Log::error('Daraja STK Query exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Format phone number to M-Pesa format (254XXXXXXXXX)
     * Made public so it can be used in controller
     */
    public function formatPhoneNumber($phoneNumber)
    {
        // Remove all non-numeric characters
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // If starts with 0, replace with 254
        if (substr($phoneNumber, 0, 1) === '0') {
            $phoneNumber = '254' . substr($phoneNumber, 1);
        }
        
        // If doesn't start with 254, add it
        if (substr($phoneNumber, 0, 3) !== '254') {
            $phoneNumber = '254' . $phoneNumber;
        }
        
        return $phoneNumber;
    }

    /**
     * Validate phone number format
     */
    public function validatePhoneNumber($phoneNumber)
    {
        $formatted = $this->formatPhoneNumber($phoneNumber);
        // Kenyan phone numbers should be 12 digits (254 + 9 digits)
        return strlen($formatted) === 12;
    }
}

