@extends('layouts.master')

@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>M-Pesa Payment</h4>
                <h6>Waiting for payment confirmation</h6>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <div class="spinner-border text-primary" role="status" style="width: 4rem; height: 4rem;">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <h4 class="mb-3">Waiting for Payment</h4>
                        <p class="text-muted mb-4">
                            A payment request has been sent to your phone. Please check your phone and enter your M-Pesa PIN to complete the payment.
                        </p>

                        <div class="alert alert-info">
                            <strong>Transaction ID:</strong> {{ $transaction->transaction_id }}<br>
                            <strong>Amount:</strong> Ksh {{ number_format($transaction->amount, 2) }}
                        </div>

                        <div class="mt-4">
                            <p class="text-muted">This page will automatically refresh to check payment status.</p>
                            <p class="text-muted">If payment is successful, you will be redirected to the receipt page.</p>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('payments.failure', $transaction->id) }}" class="btn btn-secondary">Cancel Payment</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let checkCount = 0;
const maxChecks = 60; // Check for 5 minutes (60 * 5 seconds)

function checkPaymentStatus() {
    checkCount++;
    
    if (checkCount > maxChecks) {
        alert('Payment check timeout. Please refresh the page or check your transaction status manually.');
        return;
    }

    fetch('{{ route("payments.mpesa.status", $transaction->id) }}')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'completed') {
                // Payment completed, redirect to receipt
                window.location.href = '{{ route("payments.receipt", $transaction->id) }}';
            } else if (data.status === 'failed') {
                // Payment failed, redirect to failure page
                window.location.href = '{{ route("payments.failure", $transaction->id) }}';
            } else {
                // Still pending, check again in 5 seconds
                setTimeout(checkPaymentStatus, 5000);
            }
        })
        .catch(error => {
            console.error('Error checking payment status:', error);
            // Continue checking even on error
            setTimeout(checkPaymentStatus, 5000);
        });
}

// Start checking after 10 seconds (give time for user to complete payment)
setTimeout(checkPaymentStatus, 10000);
</script>
@endsection

