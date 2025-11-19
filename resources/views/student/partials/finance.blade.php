<div class="card p-4 shadow-sm rounded-3">
    <h4 class="mb-3">Financial Information</h4>
    <div class="row">
        <!-- Term Name -->
        <div class="col-12 col-sm-4">
            <div class="form-group local-forms">
                <label>Term Name<span class="login-danger"></span></label>
                <input type="text" class="form-control @error('term_name') is-invalid @enderror"
                    name="term_name" placeholder="e.g., Term 1"
                    value="{{ old('term_name') }}">
                @error('term_name')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>

        <!-- Fee Amount -->
        <div class="col-12 col-sm-4">
            <div class="form-group local-forms">
                <label>Fee Amount<span class="login-danger"></span></label>
                <input type="text" class="form-control @error('fee_amount') is-invalid @enderror"
                    name="fee_amount" placeholder="Enter Fee Amount" value="{{ old('fee_amount') }}">
                @error('fee_amount')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>

        <!-- Financial Year -->
        <div class="col-12 col-sm-4">
            <div class="form-group local-forms">
                <label>Financial Year<span class="login-danger"></span></label>
                <input type="text" class="form-control @error('financial_year') is-invalid @enderror"
                    name="financial_year" placeholder="Enter Financial Year" value="{{ old('financial_year') }}">
                @error('financial_year')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>

        <!-- Amount Paid -->
        <div class="col-12 col-sm-4">
            <div class="form-group local-forms">
                <label>Amount Paid<span class="login-danger"></span></label>
                <input type="text" class="form-control @error('amount_paid') is-invalid @enderror"
                    name="amount_paid" placeholder="Enter Amount Paid" value="{{ old('amount_paid') }}">
                @error('amount_paid')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>

        <!-- Fee Type -->
        <div class="col-12 col-sm-4">
            <div class="form-group local-forms">
                <label>Fee Type<span class="login-danger"></span></label>
                <select class="form-control select @error('fee_type') is-invalid @enderror" name="fee_type">
                    <option selected disabled>Select Fee Type</option>
                    <option value="Tuition Fee" {{ old('fee_type') == 'Tuition Fee' ? 'selected' : '' }}>Tuition Fee</option>
                    <option value="Trip Fee" {{ old('fee_type') == 'Trip Fee' ? 'selected' : '' }}>Trip Fee</option>
                    <option value="Exam Fee" {{ old('fee_type') == 'Exam Fee' ? 'selected' : '' }}>Exam Fee</option>
                    <option value="Transport Fee" {{ old('fee_type') == 'Transport Fee' ? 'selected' : '' }}>Transport Fee</option>
                    <option value="Lunch Fees" {{ old('fee_type') == 'Lunch Fees' ? 'selected' : '' }}>Lunch Fee</option>
                </select>
                @error('fee_type')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>

        <!-- Balance -->
        <div class="col-12 col-sm-4">
            <div class="form-group local-forms">
                <label>Balance<span class="login-danger"></span></label>
                <input type="text" class="form-control @error('balance') is-invalid @enderror"
                    name="balance" readonly placeholder="Auto Calculated" value="{{ old('balance') }}">
                @error('balance')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>

        <!-- Payment Status -->
        <div class="col-12 col-sm-4">
            <div class="form-group local-forms">
                <label>Payment Status<span class="login-danger"></span></label>
                <select class="form-control select @error('payment_status') is-invalid @enderror" name="payment_status">
                    <option selected disabled>Select Payment Type</option>
                    <option value="Cash Money" {{ old('payment_status') == 'Cash Money' ? 'selected' : '' }}>Cash Money</option>
                    <option value="M-pesa" {{ old('payment_status') == 'M-pesa' ? 'selected' : '' }}>M-pesa</option>
                    <option value="Bank Payment" {{ old('payment_status') == 'Bank Payment' ? 'selected' : '' }}>Bank Payment</option>
                    <option value="Bursary" {{ old('payment_status') == 'Bursary' ? 'selected' : '' }}>Bursary</option>
                </select>
                @error('payment_status')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>

        <!-- Transaction ID -->
        <div class="col-12 col-sm-4">
            <div class="form-group local-forms">
                <label>Transaction ID<span class="login-danger"></span></label>
                <input type="text" class="form-control @error('transaction_id') is-invalid @enderror"
                    name="transaction_id" placeholder="Enter Transaction ID" value="{{ old('transaction_id') }}">
                @error('transaction_id')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>

        <!-- Date of Payment -->
        <div class="col-12 col-sm-4">
            <div class="form-group local-forms calendar-icon">
                <label>Date Of Payment <span class="login-danger">*</span></label>
                <input class="form-control datetimepicker @error('date_of_payment') is-invalid @enderror"
                    name="date_of_payment" type="text" placeholder="DD-MM-YYYY" value="{{ old('date_of_payment') }}">
                @error('date_of_payment')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>

        <!-- Next Due Date -->
        <div class="col-12 col-sm-4">
            <div class="form-group local-forms calendar-icon">
                <label>Next Due Date <span class="login-danger">*</span></label>
                <input class="form-control datetimepicker @error('next_due_date') is-invalid @enderror"
                    name="next_due_date" type="text" placeholder="DD-MM-YYYY" value="{{ old('next_due_date') }}">
                @error('next_due_date')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>

        <!-- Scholarship -->
        <div class="col-12 col-sm-4">
            <div class="form-group local-forms">
                <label>Scholarship<span class="login-danger"></span></label>
                <input type="text" class="form-control @error('scholarship') is-invalid @enderror"
                    name="scholarship" placeholder="Enter Scholarship" value="{{ old('scholarship') }}">
                @error('scholarship')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>

        <!-- Sponsor Name -->
        <div class="col-12 col-sm-4">
            <div class="form-group local-forms">
                <label>Sponsor Name<span class="login-danger"></span></label>
                <input type="text" class="form-control @error('sponsor_name') is-invalid @enderror"
                    name="sponsor_name" placeholder="Enter Sponsor Name" value="{{ old('sponsor_name') }}">
                @error('sponsor_name')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
    </div>

    <!-- Buttons (only visible in this tab) -->
    <div class="d-flex justify-content-between mt-4">
        <button type="button" class="btn btn-secondary" id="backToMedical">← Back</button>
        <button type="submit" class="btn btn-primary">Submit Registration</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Back button → go to Medical tab
    const backButton = document.getElementById('backToMedical');
    if (backButton) {
        backButton.addEventListener('click', function() {
            const prevTab = new bootstrap.Tab(document.querySelector('#medical-tab'));
            prevTab.show();
        });
    }

    // Auto calculate balance
    const feeAmount = document.querySelector('input[name="fee_amount"]');
    const amountPaid = document.querySelector('input[name="amount_paid"]');
    const balance = document.querySelector('input[name="balance"]');

    function updateBalance() {
        const fee = parseFloat(feeAmount.value) || 0;
        const paid = parseFloat(amountPaid.value) || 0;
        balance.value = (fee - paid).toFixed(2);
    }

    if (feeAmount && amountPaid && balance) {
        feeAmount.addEventListener('input', updateBalance);
        amountPaid.addEventListener('input', updateBalance);
    }
});
</script>
