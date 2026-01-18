@extends('layouts.master')
@section('content')
{{-- message --}}
{!! Toastr::message() !!}
<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col">
                    <h3 class="page-title">Financial Settings</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Financial Settings</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Financial Year & Fee Configuration</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('financial.settings.update') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Financial Year <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('financial_year') is-invalid @enderror" 
                                               name="financial_year" value="{{ old('financial_year', $settings->financial_year) }}" 
                                               placeholder="e.g., 2026" required id="financial_year_input">
                                        @error('financial_year')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <small class="form-text text-muted">The current financial/academic year</small>
                                        @if(old('financial_year', $settings->financial_year) != $settings->financial_year)
                                            <div class="form-check mt-2">
                                                <input class="form-check-input" type="checkbox" name="create_new_terms" id="create_new_terms" value="1" 
                                                       {{ old('create_new_terms') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="create_new_terms">
                                                    <strong>Create new terms for all students</strong>
                                                </label>
                                                <small class="form-text text-muted d-block">
                                                    <i class="fas fa-info-circle"></i> This will close current terms and create new terms with balances carried forward from previous terms.
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Default Fee Amount per Student (Ksh) <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" min="0" 
                                               class="form-control @error('default_fee_amount') is-invalid @enderror" 
                                               name="default_fee_amount" 
                                               value="{{ old('default_fee_amount', $settings->default_fee_amount) }}" 
                                               placeholder="0.00" required>
                                        @error('default_fee_amount')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <small class="form-text text-muted">Default fee amount that will be applied to new students</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Term Duration (Months) <span class="text-danger">*</span></label>
                                        <input type="number" min="1" max="12" 
                                               class="form-control @error('term_duration_months') is-invalid @enderror" 
                                               name="term_duration_months" 
                                               value="{{ old('term_duration_months', $settings->term_duration_months) }}" 
                                               placeholder="3" required>
                                        @error('term_duration_months')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <small class="form-text text-muted">How long each term lasts (in months)</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Terms Per Year <span class="text-danger">*</span></label>
                                        <input type="number" min="1" max="12" 
                                               class="form-control @error('terms_per_year') is-invalid @enderror" 
                                               name="terms_per_year" 
                                               value="{{ old('terms_per_year', $settings->terms_per_year) }}" 
                                               placeholder="3" required>
                                        @error('terms_per_year')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <small class="form-text text-muted">Number of terms in an academic year</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Academic Year Start Month <span class="text-danger">*</span></label>
                                        <select class="form-control @error('academic_year_start_month') is-invalid @enderror" 
                                                name="academic_year_start_month" required>
                                            @php
                                                $months = ['January', 'February', 'March', 'April', 'May', 'June', 
                                                          'July', 'August', 'September', 'October', 'November', 'December'];
                                            @endphp
                                            @foreach($months as $month)
                                                <option value="{{ $month }}" 
                                                    {{ old('academic_year_start_month', $settings->academic_year_start_month) == $month ? 'selected' : '' }}>
                                                    {{ $month }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('academic_year_start_month')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <small class="form-text text-muted">Month when the academic year begins</small>
                                    </div>
                                </div>
                            </div>

                            <div class="submit-section">
                                <button type="submit" class="btn btn-primary submit-btn">Save Settings</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Apply Settings to All Students</h5>
                    </div>
                    <div class="card-body">
                        <form id="applySettingsForm" action="{{ route('financial.settings.apply') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="apply_financial_year" id="apply_financial_year" value="1">
                                            <label class="form-check-label" for="apply_financial_year">
                                                Apply Financial Year to all students
                                            </label>
                                            <small class="form-text text-muted d-block">
                                                This will update the financial_year field for all students to: <strong>{{ $settings->financial_year }}</strong>
                                            </small>
                                        </div>
                                        <div class="form-check mt-2" id="create_new_terms_apply_container" style="display: none;">
                                            <input class="form-check-input" type="checkbox" name="create_new_terms" id="create_new_terms_apply" value="1">
                                            <label class="form-check-label" for="create_new_terms_apply">
                                                <strong>Create new terms with balance carry-over</strong>
                                            </label>
                                            <small class="form-text text-muted d-block">
                                                <i class="fas fa-info-circle"></i> This will close current terms and create new terms for all students. Any outstanding balance from previous terms will be added to the new term's fee balance.
                                            </small>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="apply_fee_amount" id="apply_fee_amount" value="1">
                                            <label class="form-check-label" for="apply_fee_amount">
                                                Apply Default Fee Amount to all students
                                            </label>
                                            <small class="form-text text-muted d-block">
                                                This will update the fee_amount field for all students to: <strong>Ksh {{ number_format($settings->default_fee_amount, 2) }}</strong>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="submit-section">
                                <button type="button" class="btn btn-warning submit-btn" id="applySettingsBtn">
                                    <i class="fas fa-exclamation-triangle me-2"></i>Apply to All Students
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Confirmation Modal --}}
<div class="modal custom-modal fade" id="confirmApplyModal" role="dialog" tabindex="-1" aria-labelledby="confirmApplyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="form-header text-center">
                    <div class="mb-3">
                        <i class="fas fa-exclamation-triangle text-warning" style="font-size: 48px;"></i>
                    </div>
                    <h3 class="mb-3">Confirm Action</h3>
                    <p class="mb-4">Are you sure you want to apply these settings to <strong>ALL students</strong>?</p>
                    <div class="alert alert-warning mb-4" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Warning:</strong> This action cannot be undone. All selected settings will be applied to every student in the system.
                    </div>
                    <div id="settingsPreview" class="text-start mb-3">
                        <p class="mb-2"><strong>Settings to be applied:</strong></p>
                        <ul class="list-unstyled ms-3" id="settingsList">
                            {{-- Will be populated by JavaScript --}}
                        </ul>
                    </div>
                </div>
                <div class="modal-btn delete-action">
                    <div class="row">
                        <div class="col-6">
                            <button type="button" class="btn btn-primary paid-cancel-btn w-100" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>Cancel
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn btn-warning w-100" id="confirmApplyBtn" style="background-color: #ffc107; border-color: #ffc107; color: #000;">
                                <i class="fas fa-check me-2"></i>Confirm & Apply
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('script')
<script>
    $(document).ready(function() {
        var originalFinancialYear = '{{ $settings->financial_year }}';
        
        // Show/hide create new terms checkbox when financial year changes
        $('#financial_year_input').on('input', function() {
            var newFinancialYear = $(this).val();
            var checkboxContainer = $('#create_new_terms').closest('.form-check');
            
            if (newFinancialYear && newFinancialYear !== originalFinancialYear) {
                // Show checkbox if it doesn't exist
                if (checkboxContainer.length === 0) {
                    var checkboxHtml = '<div class="form-check mt-2">' +
                        '<input class="form-check-input" type="checkbox" name="create_new_terms" id="create_new_terms" value="1">' +
                        '<label class="form-check-label" for="create_new_terms">' +
                        '<strong>Create new terms for all students</strong>' +
                        '</label>' +
                        '<small class="form-text text-muted d-block">' +
                        '<i class="fas fa-info-circle"></i> This will close current terms and create new terms with balances carried forward from previous terms.' +
                        '</small>' +
                        '</div>';
                    $(this).after(checkboxHtml);
                } else {
                    checkboxContainer.show();
                }
            } else {
                // Hide checkbox if financial year is same as original
                if (checkboxContainer.length > 0) {
                    checkboxContainer.hide();
                    $('#create_new_terms').prop('checked', false);
                }
            }
        });
        
        // Show/hide create new terms checkbox when apply financial year is checked
        $('#apply_financial_year').on('change', function() {
            if ($(this).is(':checked')) {
                $('#create_new_terms_apply_container').show();
            } else {
                $('#create_new_terms_apply_container').hide();
                $('#create_new_terms_apply').prop('checked', false);
            }
        });
        
        $('#applySettingsBtn').on('click', function(e) {
            e.preventDefault();
            
            // Check if at least one option is selected
            var applyFinancialYear = $('#apply_financial_year').is(':checked');
            var applyFeeAmount = $('#apply_fee_amount').is(':checked');
            
            if (!applyFinancialYear && !applyFeeAmount) {
                toastr.warning('Please select at least one option to apply.');
                return;
            }
            
            // Build settings preview
            var settingsList = [];
            if (applyFinancialYear) {
                var createNewTerms = $('#create_new_terms_apply').is(':checked');
                var yearText = '<li><i class="fas fa-calendar-alt text-primary me-2"></i>Financial Year: <strong>{{ $settings->financial_year }}</strong>';
                if (createNewTerms) {
                    yearText += ' <span class="badge bg-warning text-dark ms-2">New Terms Will Be Created</span>';
                }
                yearText += '</li>';
                settingsList.push(yearText);
            }
            if (applyFeeAmount) {
                settingsList.push('<li><i class="fas fa-money-bill-wave text-success me-2"></i>Fee Amount: <strong>Ksh {{ number_format($settings->default_fee_amount, 2) }}</strong></li>');
            }
            
            $('#settingsList').html(settingsList.join(''));
            
            // Show modal
            $('#confirmApplyModal').modal('show');
        });
        
        $('#confirmApplyBtn').on('click', function() {
            // Submit the form
            $('#applySettingsForm').submit();
        });
    });
</script>
@endsection
@endsection

