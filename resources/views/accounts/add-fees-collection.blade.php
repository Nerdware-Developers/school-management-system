@extends('layouts.master')
@section('content')
{!! Toastr::message() !!}
<div class="page-wrapper">
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Add Fees</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Accounts</a></li>
                        <li class="breadcrumb-item active">Add Fees</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Form Start -->
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('fees/collection/save') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="form-title"><span>Fees Information</span></h5>
                                </div>

                                {{-- Student Search --}}
                                <div class="col-12 col-sm-6">
                                    <div class="form-group local-forms">
                                        <label>Student Name <span class="login-danger">*</span></label>
                                        <select id="student_name" style="width: 100%;" required></select>
                                        <input type="hidden" id="student_id" name="student_id">
                                    </div>
                                </div>

                                {{-- Admission Number --}}
                                <div class="col-12 col-sm-6">
                                    <div class="form-group local-forms">
                                        <label>Admission Number</label>
                                        <input type="text" class="form-control" id="admission_number" readonly>
                                    </div>
                                </div>

                                {{-- Fee Info --}}
                                <div class="col-12 col-sm-4">
                                    <div class="form-group local-forms">
                                        <label>Fee per Term</label>
                                        <input type="text" id="fee_per_term" class="form-control" readonly>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-4">
                                    <div class="form-group local-forms">
                                        <label>Total Paid</label>
                                        <input type="text" id="total_paid" class="form-control" readonly>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-4">
                                    <div class="form-group local-forms">
                                        <label>Outstanding Balance</label>
                                        <input type="text" id="balance" class="form-control" readonly>
                                    </div>
                                </div>

                                {{-- Payment Fields --}}
                                <div class="col-12 col-sm-6">
                                    <div class="form-group local-forms">
                                        <label>Amount Paying <span class="login-danger">*</span></label>
                                        <input type="number" class="form-control" name="amount_paying" required placeholder="Enter amount to pay">
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <div class="form-group local-forms calendar-icon">
                                        <label>Paid Date <span class="login-danger">*</span></label>
                                        <input type="text" class="form-control datetimepicker" name="paid_date" placeholder="DD-MM-YYYY" required>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="student-submit text-end">
                                        <button type="button" id="payOnlineBtn" class="btn btn-success me-2" style="display: none;">
                                            <i class="fas fa-credit-card"></i> Pay Online
                                        </button>
                                        <button type="submit" class="btn btn-primary">Record Cash Payment</button>
                                    </div>
                                </div>
                            </div> {{-- end row --}}
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Form End -->
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {

    // 🔍 Initialize Select2
    $('#student_name').select2({
        placeholder: '-- Search Student --',
        ajax: {
            url: '{{ route("student.search") }}',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return { term: params.term };
            },
            processResults: function(data) {
                return { results: data };
            },
            cache: true
        }
    });

    // 🧭 When student selected
    $('#student_name').on('select2:select', function(e) {
        var data = e.params.data;
        $('#student_id').val(data.id);

        // Remove any previous hidden input for student_name
        $('input[name="student_name"]').remove();

        // Add student_name as hidden field
        $('<input>').attr({
            type: 'hidden',
            name: 'student_name',
            value: data.text
        }).appendTo('form');

        // ✅ Fetch student's fee info
        $.ajax({
            url: '/student/fees-info/' + data.id,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    toastr.success('Fee info loaded for ' + response.student);

                    // Parse numeric values safely
                    let feePerTerm = parseFloat((response.fee_per_term || "0").replace(/,/g, '')) || 0;
                    let totalPaid  = parseFloat((response.total_paid || "0").replace(/,/g, '')) || 0;
                    let balance    = parseFloat((response.balance || "0").replace(/,/g, '')) || 0;

                    // Fill in the fields
                    $('#admission_number').val(response.admission);
                    $('#fee_per_term').val(feePerTerm.toFixed(2));
                    $('#total_paid').val(totalPaid.toFixed(2));
                    $('#balance').val(balance.toFixed(2));
                } else {
                    toastr.error('Unable to load fee info');
                    $('#fee_per_term, #total_paid, #balance, #admission_number').val('');
                }
            },
            error: function() {
                toastr.error('Failed to fetch student fee info');
                $('#fee_per_term, #total_paid, #balance, #admission_number').val('');
            }
        });
    });

    // 💰 Update Outstanding Balance as user types amount
    $(document).on('input', 'input[name="amount_paying"]', function() {
        let feePerTerm = parseFloat($('#fee_per_term').val()) || 0;
        let totalPaid  = parseFloat($('#total_paid').val()) || 0;
        let amountPaying = parseFloat($(this).val()) || 0;

        // Correct balance formula (allow overpayment, clamp UI to 0)
        let newBalance = feePerTerm - (totalPaid + amountPaying);
        if (newBalance < 0) {
            newBalance = 0;
        }
        $('#balance').val(newBalance.toFixed(2));
    });

    // Show/hide online payment button when student is selected
    $('#student_name').on('select2:select', function(e) {
        var studentId = $('#student_id').val();
        if (studentId) {
            $('#payOnlineBtn').show();
        } else {
            $('#payOnlineBtn').hide();
        }
    });

    // Handle online payment button click
    $('#payOnlineBtn').on('click', function() {
        var studentId = $('#student_id').val();
        if (studentId) {
            window.location.href = '/payments/student/' + studentId;
        } else {
            toastr.error('Please select a student first');
        }
    });

});
</script>
@endsection
