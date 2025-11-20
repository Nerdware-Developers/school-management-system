@extends('layouts.master')

@section('content')
<div class="page-wrapper" style="background-color:#f9f9ff; min-height:100vh;">
    <div class="container py-4">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Teacher Profile</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('teacher/list/page') }}">Teachers</a></li>
                        <li class="breadcrumb-item active">{{ $teacher->full_name }}</li>
                    </ul>
                </div>
                <div class="col-auto">
                    <a href="{{ route('teacher/list/page') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left me-2"></i>Back to List
                    </a>
                    <a href="{{ url('teacher/edit/'.$teacher->id) }}" class="btn btn-primary">
                        <i class="far fa-edit me-2"></i>Edit
                    </a>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4" style="border-radius:15px;">
            <div class="card-body d-flex flex-wrap align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar avatar-xxl bg-primary text-white fw-bold rounded-circle d-flex align-items-center justify-content-center" style="width:90px;height:90px;">
                        {{ strtoupper(\Illuminate\Support\Str::substr($teacher->full_name, 0, 1)) }}
                    </div>
                    <div>
                        <h4 class="fw-bold mb-1">{{ $teacher->full_name }}</h4>
                        <p class="mb-0 text-muted">ID: <strong>{{ $teacher->user_id }}</strong></p>
                        <p class="mb-0 text-muted">Phone: {{ $teacher->phone_number ?? 'N/A' }}</p>
                        @if($teacher->classTeacher)
                            <p class="mb-0 text-muted">Class Teacher: <strong>{{ $teacher->classTeacher->class_name }}</strong></p>
                        @endif
                    </div>
                </div>
                <div class="row text-center mt-3 mt-md-0">
                    <div class="col">
                        <p class="text-muted mb-1">Classes</p>
                        <h4 class="fw-bold mb-0">{{ $stats['classes'] }}</h4>
                    </div>
                    <div class="col">
                        <p class="text-muted mb-1">Subjects</p>
                        <h4 class="fw-bold mb-0">{{ $stats['subjects'] }}</h4>
                    </div>
                    <div class="col">
                        <p class="text-muted mb-1">Experience</p>
                        <h4 class="fw-bold mb-0">{{ $teacher->experience ?? 'N/A' }}</h4>
                    </div>
                    <div class="col">
                        <p class="text-muted mb-1">Total Paid</p>
                        <h4 class="fw-bold mb-0 text-success">Ksh{{ number_format($paymentStats['total_paid'], 2) }}</h4>
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

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Teaching Assignments</h5>
                        @if($assignments->isEmpty())
                            <p class="text-muted mb-0">No subject assignments recorded for this teacher.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-striped align-middle">
                                    <thead>
                                        <tr>
                                            <th>Subject</th>
                                            <th>Class</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($assignments as $assignment)
                                            <tr>
                                                <td>{{ $assignment['subject'] ?? '—' }}</td>
                                                <td>{{ $assignment['class'] ?? '—' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Personal Information</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item px-0 d-flex justify-content-between">
                                <span class="text-muted">Gender</span>
                                <span>{{ $teacher->gender ?? 'N/A' }}</span>
                            </li>
                            <li class="list-group-item px-0 d-flex justify-content-between">
                                <span class="text-muted">Date of Birth</span>
                                <span>{{ $teacher->date_of_birth ?? 'N/A' }}</span>
                            </li>
                            <li class="list-group-item px-0 d-flex justify-content-between">
                                <span class="text-muted">Qualification</span>
                                <span>{{ $teacher->qualification ?? 'N/A' }}</span>
                            </li>
                            <li class="list-group-item px-0 d-flex justify-content-between">
                                <span class="text-muted">Monthly Salary</span>
                                <span><strong>{{ $teacher->monthly_salary ? 'Ksh' . number_format($teacher->monthly_salary, 2) : 'N/A' }}</strong></span>
                            </li>
                            <li class="list-group-item px-0 d-flex justify-content-between">
                                <span class="text-muted">Phone</span>
                                <span>{{ $teacher->phone_number ?? 'N/A' }}</span>
                            </li>
                            <li class="list-group-item px-0 d-flex justify-content-between">
                                <span class="text-muted">Address</span>
                                <span class="text-end">{{ $teacher->address ?? 'N/A' }}</span>
                            </li>
                            <li class="list-group-item px-0 d-flex justify-content-between">
                                <span class="text-muted">City</span>
                                <span>{{ $teacher->city ?? 'N/A' }}</span>
                            </li>
                            <li class="list-group-item px-0 d-flex justify-content-between">
                                <span class="text-muted">Country</span>
                                <span>{{ $teacher->country ?? 'N/A' }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Notes</h5>
                        <p class="text-muted mb-0">
                            {{ $teacher->notes ?? 'No additional notes for this teacher.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment History -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Payment History</h5>
                    <a href="{{ route('account/salary') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-plus me-1"></i>Add Payment
                    </a>
                </div>
                @if($paymentHistory->isEmpty())
                    <p class="text-muted mb-0">No payment records found for this teacher.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Payment Date</th>
                                    <th>Month Reference</th>
                                    <th>Amount</th>
                                    <th>Payment Method</th>
                                    <th>Reference</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($paymentHistory as $payment)
                                    <tr>
                                        <td>{{ $payment->payment_date ? $payment->payment_date->format('d M Y') : 'N/A' }}</td>
                                        <td>{{ $payment->month_reference ?? 'N/A' }}</td>
                                        <td class="fw-bold text-success">Ksh{{ number_format($payment->amount, 2) }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $payment->payment_method ?? 'N/A' }}</span>
                                        </td>
                                        <td>{{ $payment->reference ?? '—' }}</td>
                                        <td class="text-muted small">{{ Str::limit($payment->notes ?? '—', 50) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="fw-bold">
                                    <td colspan="2" class="text-end">Total:</td>
                                    <td class="text-success">Ksh{{ number_format($paymentStats['total_paid'], 2) }}</td>
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

