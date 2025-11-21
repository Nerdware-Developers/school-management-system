@extends('layouts.master')

@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Complete Payment</h4>
                <h6>Secure payment processing</h6>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-4">Payment Information</h5>
                        
                        <div class="alert alert-info">
                            <strong>Amount to Pay:</strong> Ksh {{ number_format($transaction->amount, 2) }}
                        </div>

                        <form id="payment-form">
                            <div id="payment-element">
                                <!-- Stripe Elements will create form elements here -->
                            </div>
                            
                            <button type="submit" id="submit-button" class="btn btn-primary btn-lg w-100 mt-4">
                                <span id="button-text">Pay Ksh {{ number_format($transaction->amount, 2) }}</span>
                                <span id="spinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
                            </button>
                            
                            <div id="payment-message" class="mt-3"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
const stripe = Stripe('{{ $stripeKey }}');
const clientSecret = '{{ $paymentIntent->client_secret }}';

const elements = stripe.elements({
    clientSecret: clientSecret,
    appearance: {
        theme: 'stripe',
    }
});

const paymentElement = elements.create('payment');
paymentElement.mount('#payment-element');

const form = document.getElementById('payment-form');
const submitButton = document.getElementById('submit-button');
const buttonText = document.getElementById('button-text');
const spinner = document.getElementById('spinner');
const paymentMessage = document.getElementById('payment-message');

form.addEventListener('submit', async (event) => {
    event.preventDefault();
    
    submitButton.disabled = true;
    buttonText.classList.add('d-none');
    spinner.classList.remove('d-none');
    paymentMessage.textContent = '';

    const {error, paymentIntent} = await stripe.confirmPayment({
        elements,
        confirmParams: {
            return_url: '{{ route("payments.success") }}?transaction_id={{ $transaction->transaction_id }}&payment_intent={{ $paymentIntent->id }}',
        },
        redirect: 'if_required'
    });

    if (error) {
        paymentMessage.textContent = error.message;
        paymentMessage.className = 'alert alert-danger';
        submitButton.disabled = false;
        buttonText.classList.remove('d-none');
        spinner.classList.add('d-none');
    } else if (paymentIntent && paymentIntent.status === 'succeeded') {
        paymentMessage.textContent = 'Payment successful! Redirecting...';
        paymentMessage.className = 'alert alert-success';
        window.location.href = '{{ route("payments.success") }}?transaction_id={{ $transaction->transaction_id }}&payment_intent={{ $paymentIntent->id }}';
    }
});
</script>
@endsection

