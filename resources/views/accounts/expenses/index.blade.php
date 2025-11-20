@extends('layouts.master')

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Expenses</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('account/fees/collections/page') }}">Accounts</a></li>
                        <li class="breadcrumb-item active">Expenses</li>
                    </ul>
                </div>
                <div class="col-auto">
                    <a href="{{ route('account/expenses/create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add Expense
                    </a>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm border-0" style="background-color:#e0f2fe;">
                    <div class="card-body">
                        <p class="text-muted mb-1">This Period</p>
                        <h3 class="fw-bold mb-0">Ksh{{ number_format($monthlyTotal, 2) }}</h3>
                        <form method="GET" class="mt-2">
                            <label class="form-label small text-muted mb-1">Filter by month</label>
                            <input type="month" name="month" value="{{ $month }}" class="form-control" onchange="this.form.submit()">
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0" style="background-color:#dcfce7;">
                    <div class="card-body">
                        <p class="text-muted mb-1">All-Time Expenses</p>
                        <h3 class="fw-bold text-success mb-0">Ksh{{ number_format($overallTotal, 2) }}</h3>
                        <p class="small text-muted mb-0">Recorded expenses to date</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Expense History</h5>
                    <span class="badge bg-light text-dark">{{ $expenses->total() }} items</span>
                </div>

                @if($expenses->isEmpty())
                    <p class="text-muted mb-0">No expenses recorded yet.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Paid To</th>
                                    <th>Reference</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($expenses as $expense)
                                    <tr>
                                        <td>{{ $expense->expense_date->format('M d, Y') }}</td>
                                        <td>
                                            <strong>{{ $expense->title }}</strong>
                                            @if($expense->notes)
                                                <div class="text-muted small">{{ \Illuminate\Support\Str::limit($expense->notes, 60) }}</div>
                                            @endif
                                        </td>
                                        <td>{{ $expense->category ?? '—' }}</td>
                                        <td>{{ $expense->paid_to ?? '—' }}</td>
                                        <td>{{ $expense->reference ?? '—' }}</td>
                                        <td class="text-end fw-semibold">Ksh{{ number_format($expense->amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end">{{ $expenses->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

