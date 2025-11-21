@extends('layouts.master')

@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Online Payment</h4>
                <h6>Pay fees online using Stripe</h6>
            </div>
            <div class="page-btn">
                <a href="{{ route('account/fees/collections/page') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-4">Payment Details</h5>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>Student Name:</strong></p>
                                <p class="text-muted">{{ $student->first_name }} {{ $student->last_name }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Admission Number:</strong></p>
                                <p class="text-muted">{{ $student->admission_number }}</p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>Class:</strong></p>
                                <p class="text-muted">{{ $student->class }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Current Balance:</strong></p>
                                <p class="text-danger fw-bold">Ksh {{ number_format($balance, 2) }}</p>
                            </div>
                        </div>

                        <hr>

                        <form action="{{ route('payments.initiate') }}" method="POST" id="paymentForm">
                            @csrf
                            <input type="hidden" name="student_id" value="{{ $student->id }}">

                            <div class="form-group mb-3">
                                <label>Payment Method <span class="text-danger">*</span></label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check payment-method-option">
                                            <input class="form-check-input" type="radio" name="payment_method" id="payment_stripe" value="stripe" checked>
                                            <label class="form-check-label" for="payment_stripe">
                                                <i class="fas fa-credit-card"></i> Card Payment (Stripe)
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check payment-method-option">
                                            <input class="form-check-input" type="radio" name="payment_method" id="payment_mpesa" value="mpesa">
                                            <label class="form-check-label" for="payment_mpesa">
                                                <i class="fas fa-mobile-alt"></i> M-Pesa
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3" id="phoneNumberField" style="display: none;">
                                <label>M-Pesa Phone Number <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control" 
                                       name="phone_number" 
                                       id="phone_number"
                                       placeholder="0712345678 or 254712345678"
                                       value="{{ $student->parent_number ?? '' }}">
                                <small class="text-muted">Enter your M-Pesa registered phone number</small>
                            </div>

                            <div class="form-group mb-3">
                                <label>Payment Amount (KES) <span class="text-danger">*</span></label>
                                <input type="number" 
                                       class="form-control" 
                                       name="amount" 
                                       id="amount"
                                       step="0.01"
                                       min="1"
                                       @if($balance > 0) max="{{ $balance }}" @endif
                                       value="{{ $balance > 0 ? $balance : '' }}"
                                       placeholder="Enter amount to pay"
                                       required>
                                <small class="text-muted">
                                    @if($balance > 0)
                                        Maximum: Ksh {{ number_format($balance, 2) }}
                                    @else
                                        Enter the amount you want to pay
                                    @endif
                                </small>
                            </div>

                            <div class="form-group mb-3">
                                <label>Description (Optional)</label>
                                <textarea class="form-control" name="description" rows="3" placeholder="Payment description..."></textarea>
                            </div>

                            <div class="alert alert-info" id="paymentInfo">
                                <i class="fas fa-info-circle"></i> 
                                <span id="paymentInfoText">You will be redirected to a secure payment page to complete your transaction.</span>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100" id="submitBtn">
                                <i class="fas fa-credit-card"></i> <span id="submitBtnText">Proceed to Payment</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-3">Payment Summary</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Fee Amount:</span>
                            <strong>Ksh {{ number_format($currentTerm->fee_amount ?? 0, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Amount Paid:</span>
                            <strong class="text-success">Ksh {{ number_format($currentTerm->amount_paid ?? 0, 2) }}</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span><strong>Balance:</strong></span>
                            <strong class="text-danger">Ksh {{ number_format($balance, 2) }}</strong>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <h6 class="mb-3">Secure Payment</h6>
                        <p class="small text-muted">
                            <i class="fas fa-lock"></i> Your payment is secured by Stripe. 
                            We do not store your card details.
                        </p>
                        <div class="d-flex gap-2 mt-3">
                            <img src="https://img.shields.io/badge/Stripe-626CD9?style=for-the-badge&logo=Stripe&logoColor=white" alt="Stripe" style="height: 30px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('amount').addEventListener('input', function() {
    const max = parseFloat(this.max);
    const value = parseFloat(this.value);
    
    // Only validate max if max is set and greater than 0
    if (max && max > 0 && value > max) {
        this.value = max;
        alert('Amount cannot exceed balance of Ksh ' + max.toFixed(2));
    }
    
    // Ensure minimum value
    if (value < 1 && value !== '') {
        this.value = 1;
    }
});

// Handle payment method change
document.querySelectorAll('input[name="payment_method"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        const phoneField = document.getElementById('phoneNumberField');
        const phoneInput = document.getElementById('phone_number');
        const paymentInfo = document.getElementById('paymentInfoText');
        const submitBtn = document.getElementById('submitBtn');
        const submitBtnText = document.getElementById('submitBtnText');
        const submitBtnIcon = submitBtn.querySelector('i');

        if (this.value === 'mpesa') {
            phoneField.style.display = 'block';
            phoneInput.required = true;
            paymentInfo.textContent = 'You will receive an M-Pesa prompt on your phone. Enter your M-Pesa PIN to complete the payment.';
            submitBtnText.textContent = 'Pay with M-Pesa';
            submitBtnIcon.className = 'fas fa-mobile-alt';
            submitBtn.classList.remove('btn-primary');
            submitBtn.classList.add('btn-success');
        } else {
            phoneField.style.display = 'none';
            phoneInput.required = false;
            paymentInfo.textContent = 'You will be redirected to a secure payment page to complete your transaction.';
            submitBtnText.textContent = 'Proceed to Payment';
            submitBtnIcon.className = 'fas fa-credit-card';
            submitBtn.classList.remove('btn-success');
            submitBtn.classList.add('btn-primary');
        }
    });
});
</script>
@endsection

