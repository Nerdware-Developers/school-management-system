@extends('layouts.master')

@section('content')
<div class="page-wrapper" style="background-color:#f9f9ff; min-height:100vh;">
    <div class="container py-4">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Employer Profile</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('employers.index') }}">Employers</a></li>
                        <li class="breadcrumb-item active">{{ $employer->full_name }}</li>
                    </ul>
                </div>
                <div class="col-auto">
                    <a href="{{ route('employers.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left me-2"></i>Back to List
                    </a>
                    <a href="{{ route('employers.edit', $employer->id) }}" class="btn btn-primary">
                        <i class="far fa-edit me-2"></i>Edit
                    </a>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4" style="border-radius:15px;">
            <div class="card-body d-flex flex-wrap align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar avatar-xxl bg-primary text-white fw-bold rounded-circle d-flex align-items-center justify-content-center" style="width:90px;height:90px;">
                        {{ strtoupper(\Illuminate\Support\Str::substr($employer->full_name, 0, 1)) }}
                    </div>
                    <div>
                        <h4 class="fw-bold mb-1">{{ $employer->full_name }}</h4>
                        <p class="mb-0 text-muted">Employee ID: <strong>{{ $employer->employee_id }}</strong></p>
                        <p class="mb-0 text-muted">Position: <strong>{{ $employer->position ?? 'N/A' }}</strong></p>
                        <p class="mb-0 text-muted">Department: <strong>{{ $employer->department ?? 'N/A' }}</strong></p>
                        <p class="mb-0 text-muted">Phone: {{ $employer->phone_number ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="row text-center mt-3 mt-md-0">
                    <div class="col">
                        <p class="text-muted mb-1">Total Payments</p>
                        <h4 class="fw-bold mb-0 text-success">Ksh{{ number_format($paymentStats['total_paid'], 2) }}</h4>
                    </div>
                    <div class="col">
                        <p class="text-muted mb-1">This Year</p>
                        <h4 class="fw-bold mb-0">Ksh{{ number_format($paymentStats['this_year'], 2) }}</h4>
                    </div>
                    <div class="col">
                        <p class="text-muted mb-1">This Month</p>
                        <h4 class="fw-bold mb-0">Ksh{{ number_format($paymentStats['this_month'], 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Statistics -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm" style="background-color:#e0f2fe;">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-1">Total Payments</h6>
                        <h3 class="fw-bold">Ksh{{ number_format($paymentStats['total_paid'], 2) }}</h3>
                        <p class="small text-muted mb-0">{{ $paymentStats['total_payments'] }} Records</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm" style="background-color:#dcfce7;">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-1">This Year</h6>
                        <h3 class="fw-bold text-success">Ksh{{ number_format($paymentStats['this_year'], 2) }}</h3>
                        <p class="small text-muted mb-0">{{ now()->format('Y') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm" style="background-color:#fff7e6;">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-1">This Month</h6>
                        <h3 class="fw-bold text-warning">Ksh{{ number_format($paymentStats['this_month'], 2) }}</h3>
                        <p class="small text-muted mb-0">{{ now()->format('F Y') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm" style="background-color:#fee2e2;">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-1">Average Payment</h6>
                        <h3 class="fw-bold text-danger">Ksh{{ $paymentStats['total_payments'] > 0 ? number_format($paymentStats['total_paid'] / $paymentStats['total_payments'], 2) : '0.00' }}</h3>
                        <p class="small text-muted mb-0">Per Payment</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Personal Information -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Personal Information</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td class="text-muted" style="width:40%;">Full Name:</td>
                                <td><strong>{{ $employer->full_name }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Employee ID:</td>
                                <td><strong>{{ $employer->employee_id }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Gender:</td>
                                <td>{{ $employer->gender ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Date of Birth:</td>
                                <td>{{ $employer->date_of_birth ? $employer->date_of_birth->format('M d, Y') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Position:</td>
                                <td>{{ $employer->position ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Department:</td>
                                <td>{{ $employer->department ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Joining Date:</td>
                                <td>{{ $employer->joining_date ? $employer->joining_date->format('M d, Y') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Monthly Salary:</td>
                                <td><strong>{{ $employer->monthly_salary ? 'Ksh' . number_format($employer->monthly_salary, 2) : 'N/A' }}</strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Contact Information</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td class="text-muted" style="width:40%;">Phone Number:</td>
                                <td>{{ $employer->phone_number ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Email:</td>
                                <td>{{ $employer->email ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Address:</td>
                                <td>{{ $employer->address ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">City:</td>
                                <td>{{ $employer->city ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">State:</td>
                                <td>{{ $employer->state ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Zip Code:</td>
                                <td>{{ $employer->zip_code ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Country:</td>
                                <td>{{ $employer->country ?? 'N/A' }}</td>
                            </tr>
                        </table>
                        @if($employer->notes)
                        <div class="mt-3">
                            <h6 class="text-muted">Notes:</h6>
                            <p class="mb-0">{{ $employer->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment History -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Payment History</h5>
                    <a href="{{ route('account/salary/create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-2"></i>Add Payment
                    </a>
                </div>
                @if($paymentHistory->isEmpty())
                    <p class="text-muted mb-0">No salary payments recorded for this employer.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Reference</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($paymentHistory as $payment)
                                    <tr>
                                        <td>{{ $payment->month_reference ?? 'N/A' }}</td>
                                        <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                        <td><strong>Ksh{{ number_format($payment->amount, 2) }}</strong></td>
                                        <td>{{ $payment->payment_method ?? 'N/A' }}</td>
                                        <td>{{ $payment->reference ?? 'N/A' }}</td>
                                        <td>{{ \Illuminate\Support\Str::limit($payment->notes ?? 'N/A', 30) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-info">
                                    <td colspan="2"><strong>Total</strong></td>
                                    <td><strong>Ksh{{ number_format($paymentHistory->sum('amount'), 2) }}</strong></td>
                                    <td colspan="3"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

