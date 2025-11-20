@extends('layouts.master')

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Salary Payments</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('account/fees/collections/page') }}">Accounts</a></li>
                        <li class="breadcrumb-item active">Salary</li>
                    </ul>
                </div>
                <div class="col-auto">
                    <a href="{{ route('account/salary/create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Record Salary
                    </a>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm border-0" style="background-color:#dcfce7;">
                    <div class="card-body">
                        <p class="text-muted mb-1">This Period</p>
                        <h3 class="fw-bold text-success mb-0">Ksh{{ number_format($monthlyTotal, 2) }}</h3>
                        <form method="GET" class="mt-2">
                            <label class="form-label small text-muted mb-1">Filter by month</label>
                            <input type="month" name="month" value="{{ $month }}" class="form-control" onchange="this.form.submit()">
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0" style="background-color:#fff7e6;">
                    <div class="card-body">
                        <p class="text-muted mb-1">Total Payroll</p>
                        <h3 class="fw-bold text-warning mb-0">Ksh{{ number_format($overallTotal, 2) }}</h3>
                        <p class="small text-muted mb-0">All recorded salary payouts</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Salary History</h5>
                    <span class="badge bg-light text-dark">{{ $salaryPayments->total() }} records</span>
                </div>

                @if($salaryPayments->isEmpty())
                    <p class="text-muted mb-0">No salary payments recorded yet.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Staff</th>
                                    <th>Role</th>
                                    <th>Month</th>
                                    <th>Method</th>
                                    <th>Reference</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($salaryPayments as $payment)
                                    <tr>
                                        <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                        <td>{{ $payment->staff_name }}</td>
                                        <td>{{ $payment->role ?? '—' }}</td>
                                        <td>{{ $payment->month_reference ?? '—' }}</td>
                                        <td>{{ $payment->payment_method ?? '—' }}</td>
                                        <td>{{ $payment->reference ?? '—' }}</td>
                                        <td class="text-end fw-semibold">Ksh{{ number_format($payment->amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end">{{ $salaryPayments->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

