<div class="card p-4 shadow-sm rounded-3">
    <h4 class="mb-3">Financial Information</h4>
    
    <!-- Transport Section -->
    <div class="card mb-4" style="background-color: #f8f9fa;">
        <div class="card-body">
            <h5 class="mb-3">Transport Information</h5>
            <div class="row">
                <div class="col-12 mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="uses_transport" id="uses_transport" value="1" {{ old('uses_transport') ? 'checked' : '' }}>
                        <label class="form-check-label" for="uses_transport">
                            <strong>Student Uses Transport</strong>
                        </label>
                    </div>
                </div>
            </div>

            <div id="transport_section_container" style="display: none;">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label">Select Transport Section <span class="login-danger">*</span></label>
                    </div>
                    
                    <!-- Section 1 -->
                    <div class="col-md-4 mb-3">
                        <div class="card border-primary">
                            <div class="card-body">
                                <div class="form-check">
                                    <input class="form-check-input transport-section" type="radio" name="transport_section" id="transport_section_1" value="1" {{ old('transport_section') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="transport_section_1">
                                        <strong>Section 1</strong>
                                    </label>
                                </div>
                                <small class="text-muted d-block mt-2">
                                    MUWA, BARAKA, KANGEMA, UMOJA ONE, URITHI
                                </small>
                                <div class="mt-2">
                                    <strong class="text-primary">Fee: Ksh 3,000/=</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2 -->
                    <div class="col-md-4 mb-3">
                        <div class="card border-success">
                            <div class="card-body">
                                <div class="form-check">
                                    <input class="form-check-input transport-section" type="radio" name="transport_section" id="transport_section_2" value="2" {{ old('transport_section') == '2' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="transport_section_2">
                                        <strong>Section 2</strong>
                                    </label>
                                </div>
                                <small class="text-muted d-block mt-2">
                                    KIAMUNYEKI, MURUNYU, MODERN, UMOJA TWO
                                </small>
                                <div class="mt-2">
                                    <strong class="text-success">Fee: Ksh 4,000/=</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3 -->
                    <div class="col-md-4 mb-3">
                        <div class="card border-warning">
                            <div class="card-body">
                                <div class="form-check">
                                    <input class="form-check-input transport-section" type="radio" name="transport_section" id="transport_section_3" value="3" {{ old('transport_section') == '3' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="transport_section_3">
                                        <strong>Section 3</strong>
                                    </label>
                                </div>
                                <small class="text-muted d-block mt-2">
                                    ST. GABRIEL, KIRATINA, FREE AREA, PIPE LINE
                                </small>
                                <div class="mt-2">
                                    <strong class="text-warning">Fee: Ksh 6,000/=</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <strong>Transport Fee:</strong> <span id="transport_fee_display">Ksh 0/=</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Term Name -->
        <div class="col-12 col-sm-4">
            <div class="form-group local-forms">
                <label>Term Name<span class="login-danger"></span></label>
                <input type="text" class="form-control @error('term_name') is-invalid @enderror"
                    name="term_name" placeholder="e.g., Term 1"
                    value="{{ old('term_name', $currentTerm ?? '') }}">
                <small class="form-text text-muted">Auto-filled based on current date (can be changed)</small>
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
                    name="financial_year" placeholder="Enter Financial Year" 
                    value="{{ old('financial_year', $currentAcademicYear ?? '') }}">
                <small class="form-text text-muted">Auto-filled based on current date (can be changed)</small>
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

        <!-- Total Fee (Tuition + Transport) -->
        <div class="col-12 col-sm-4">
            <div class="form-group local-forms">
                <label>Total Fee (Tuition + Transport)<span class="login-danger"></span></label>
                <input type="text" class="form-control" id="total_fee_display" readonly placeholder="Auto Calculated" value="0">
                <small class="text-muted">This includes tuition fee and transport fee (if applicable)</small>
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

    // Transport fee constants
    const TRANSPORT_FEES = {
        1: 3000,  // Section 1
        2: 4000,  // Section 2
        3: 6000   // Section 3
    };

    // Get elements
    const usesTransport = document.getElementById('uses_transport');
    const transportSectionContainer = document.getElementById('transport_section_container');
    const transportSections = document.querySelectorAll('.transport-section');
    const transportFeeDisplay = document.getElementById('transport_fee_display');
    const feeAmount = document.querySelector('input[name="fee_amount"]');
    const amountPaid = document.querySelector('input[name="amount_paid"]');
    const balance = document.querySelector('input[name="balance"]');
    const totalFeeDisplay = document.getElementById('total_fee_display');

    // Toggle transport section visibility
    function toggleTransportSection() {
        if (usesTransport.checked) {
            transportSectionContainer.style.display = 'block';
            // Require transport section selection
            transportSections.forEach(section => {
                section.required = true;
            });
        } else {
            transportSectionContainer.style.display = 'none';
            // Uncheck all transport sections
            transportSections.forEach(section => {
                section.checked = false;
                section.required = false;
            });
            updateTransportFee();
        }
    }

    // Update transport fee display
    function updateTransportFee() {
        let transportFee = 0;
        if (usesTransport.checked) {
            transportSections.forEach(section => {
                if (section.checked) {
                    transportFee = TRANSPORT_FEES[parseInt(section.value)];
                }
            });
        }
        transportFeeDisplay.textContent = 'Ksh ' + transportFee.toLocaleString('en-KE') + '/=';
        updateTotalFee();
    }

    // Update total fee (tuition + transport)
    function updateTotalFee() {
        const tuitionFee = parseFloat(feeAmount.value) || 0;
        let transportFee = 0;
        
        if (usesTransport.checked) {
            transportSections.forEach(section => {
                if (section.checked) {
                    transportFee = TRANSPORT_FEES[parseInt(section.value)];
                }
            });
        }
        
        const totalFee = tuitionFee + transportFee;
        totalFeeDisplay.value = totalFee.toFixed(2);
        
        // Update balance calculation
        updateBalance();
    }

    // Auto calculate balance
    function updateBalance() {
        const tuitionFee = parseFloat(feeAmount.value) || 0;
        let transportFee = 0;
        
        if (usesTransport.checked) {
            transportSections.forEach(section => {
                if (section.checked) {
                    transportFee = TRANSPORT_FEES[parseInt(section.value)];
                }
            });
        }
        
        const totalFee = tuitionFee + transportFee;
        const paid = parseFloat(amountPaid.value) || 0;
        const calculatedBalance = totalFee - paid;
        
        balance.value = calculatedBalance.toFixed(2);
    }

    // Event listeners
    if (usesTransport) {
        usesTransport.addEventListener('change', toggleTransportSection);
        toggleTransportSection(); // Initialize on page load
    }

    transportSections.forEach(section => {
        section.addEventListener('change', updateTransportFee);
    });

    if (feeAmount && amountPaid && balance) {
        feeAmount.addEventListener('input', updateTotalFee);
        amountPaid.addEventListener('input', updateBalance);
    }

    // Initialize calculations on page load
    updateTransportFee();
    updateTotalFee();
});
</script>
