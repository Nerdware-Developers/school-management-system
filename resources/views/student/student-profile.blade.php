@extends('layouts.master')

@section('content')
<div class="page-wrapper" style="background-color:#f9f9ff; min-height:100vh;">
    <div class="container py-4">

        <!-- Header -->
        <h4 class="fw-bold mb-4">Student Profile</h4>

        <!-- Profile Summary Card -->
        <div class="card shadow-sm border-0 mb-4" style="border-radius:15px;">
            <div class="card-body d-flex flex-wrap align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ $studentProfile->image 
                            ? route('student.photo', $studentProfile->image) 
                            : asset('images/photo_defaults.jpg') }}"
                        class="rounded-circle shadow-sm"
                        width="100" height="100" />
                    <div>
                        <h4 class="fw-bold mb-1">{{ $studentProfile->first_name }} {{ $studentProfile->last_name }}</h4>
                        <p class="mb-0 text-muted">Admission No: <strong>{{ $studentProfile->admission_number }}</strong></p>
                        <p class="mb-0 text-muted">Class: {{ $studentProfile->class }}</p>
                    </div>
                </div>
                <div class="text-end mt-3 mt-md-0">
                    <a href="{{ route('student/edit', $studentProfile->id) }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-pencil-square me-1"></i> Edit
                    </a>
                </div>
            </div>
        </div>

        <!-- Financial Summary -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm" style="background-color:#e0f2fe;">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-1">Current Term Fee</h6>
                        <h3 class="fw-bold">Ksh{{ number_format($feePerTerm, 2) }}</h3>
                        <p class="small text-muted mb-0">{{ optional($currentTerm)->term_name ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm" style="background-color:#dcfce7;">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-1">Paid This Term</h6>
                        <h3 class="fw-bold">Ksh{{ number_format($amountPaid, 2) }}</h3>
                        <p class="small text-muted mb-0">Academic Year: {{ optional($currentTerm)->academic_year ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm" style="background-color:#fee2e2;">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-1">Outstanding Balance</h6>
                        <h3 class="fw-bold text-danger">Ksh{{ number_format($balance, 2) }}</h3>
                        <p class="small text-muted mb-0">
                            Updated {{ optional(optional($currentTerm)->updated_at)->diffForHumans() ?? 'N/A' }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm" style="background-color:#fff7e6;">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-1">Carried Forward</h6>
                        <h3 class="fw-bold text-warning">Ksh{{ number_format($financialSummary['carried_balance'], 2) }}</h3>
                        <p class="small text-muted mb-0">Prev Balance: Ksh{{ number_format($financialSummary['previous_balance'], 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Finance Actions -->
        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">Start New Term</h5>
                            <span class="badge bg-light text-dark">Outstanding: Ksh{{ number_format($balance, 2) }}</span>
                        </div>
                        <form method="POST" action="{{ route('student.terms.store', $studentProfile->id) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Term Name</label>
                                <input type="text" name="term_name" class="form-control @error('term_name', 'termCreation') is-invalid @enderror"
                                    placeholder="e.g., Term 2" value="{{ old('term_name') }}">
                                @error('term_name', 'termCreation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Academic Year</label>
                                <input type="text" name="academic_year" class="form-control @error('academic_year', 'termCreation') is-invalid @enderror"
                                    placeholder="e.g., 2025" value="{{ old('academic_year', $studentProfile->financial_year) }}">
                                @error('academic_year', 'termCreation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Fee Amount</label>
                                <input type="number" step="0.01" name="fee_amount" class="form-control @error('fee_amount', 'termCreation') is-invalid @enderror"
                                    placeholder="Enter term amount" value="{{ old('fee_amount') }}">
                                @error('fee_amount', 'termCreation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control @error('notes', 'termCreation') is-invalid @enderror" name="notes" rows="2" placeholder="Optional notes">{{ old('notes') }}</textarea>
                                @error('notes', 'termCreation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Create Term</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">Record Payment</h5>
                            <span class="badge bg-light text-dark">Current Term: {{ optional($currentTerm)->term_name ?? 'N/A' }}</span>
                        </div>
                        @if($currentTerm)
                            <p class="text-muted small mb-3">
                                Outstanding: <strong>Ksh{{ number_format($currentTerm->closing_balance, 2) }}</strong> |
                                Opening Balance: Ksh{{ number_format($currentTerm->opening_balance, 2) }}
                            </p>
                            <form method="POST" action="{{ route('student.terms.payment', [$studentProfile->id, $currentTerm->id]) }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Amount</label>
                                    <input type="number" step="0.01" name="amount" class="form-control @error('amount', 'termPayment') is-invalid @enderror"
                                        placeholder="Amount received" value="{{ old('amount') }}">
                                    @error('amount', 'termPayment')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Payment Method</label>
                                    <select name="payment_method" class="form-control @error('payment_method', 'termPayment') is-invalid @enderror">
                                        <option value="" disabled {{ old('payment_method') ? '' : 'selected' }}>Select method</option>
                                        <option value="Cash Money" {{ old('payment_method') == 'Cash Money' ? 'selected' : '' }}>Cash Money</option>
                                        <option value="M-pesa" {{ old('payment_method') == 'M-pesa' ? 'selected' : '' }}>M-pesa</option>
                                        <option value="Bank Payment" {{ old('payment_method') == 'Bank Payment' ? 'selected' : '' }}>Bank Payment</option>
                                        <option value="Bursary" {{ old('payment_method') == 'Bursary' ? 'selected' : '' }}>Bursary</option>
                                    </select>
                                    @error('payment_method', 'termPayment')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Reference</label>
                                    <input type="text" name="payment_reference" class="form-control @error('payment_reference', 'termPayment') is-invalid @enderror"
                                        placeholder="Receipt / Transaction ID" value="{{ old('payment_reference') }}">
                                    @error('payment_reference', 'termPayment')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Notes</label>
                                    <textarea class="form-control @error('notes', 'termPayment') is-invalid @enderror" name="notes" rows="2" placeholder="Optional notes">{{ old('notes') }}</textarea>
                                    @error('notes', 'termPayment')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-success">Record Payment</button>
                                </div>
                            </form>
                        @else
                            <p class="text-muted mb-0">No active term found. Please create a term first.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Term History -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Term History</h5>
                @if($feeTerms->isEmpty())
                    <p class="text-muted mb-0">No finance records found for this student.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Term</th>
                                    <th>Year</th>
                                    <th>Opening</th>
                                    <th>Fee</th>
                                    <th>Paid</th>
                                    <th>Balance</th>
                                    <th>Status</th>
                                    <th>Last Payment</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($feeTerms as $term)
                                    <tr>
                                        <td>{{ $term->term_name }}</td>
                                        <td>{{ $term->academic_year }}</td>
                                        <td>Ksh{{ number_format($term->opening_balance, 2) }}</td>
                                        <td>Ksh{{ number_format($term->fee_amount, 2) }}</td>
                                        <td>Ksh{{ number_format($term->amount_paid, 2) }}</td>
                                        <td>
                                            <span class="fw-bold {{ $term->closing_balance > 0 ? 'text-danger' : 'text-success' }}">
                                                Ksh{{ number_format($term->closing_balance, 2) }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $badgeClass = match($term->status) {
                                                    'current' => 'bg-primary',
                                                    'carried' => 'bg-warning text-dark',
                                                    default => 'bg-secondary',
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">
                                                {{ ucfirst($term->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($term->last_payment_at)
                                                {{ $term->last_payment_at->format('d M Y') }}
                                                <div class="small text-muted">{{ $term->last_payment_method }}</div>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- Payment History -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Payment History</h5>
                @if($payments->isEmpty())
                    <p class="text-muted mb-0">No payments recorded for this student.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Type / Method</th>
                                    <th>Term</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payments as $payment)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($payment->paid_date)->format('d M Y') }}</td>
                                        <td>Ksh{{ number_format($payment->fees_amount, 2) }}</td>
                                        <td>{{ $payment->fees_type }}</td>
                                        <td>
                                            @if($payment->term)
                                                {{ $payment->term->term_name }} ({{ $payment->term->academic_year }})
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- Personal & Parent Info -->
        <div class="row g-4">
            <!-- Personal Info -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0" style="border-radius:15px;">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Personal Information</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>Date of Birth:</strong> <span>{{ $studentProfile->date_of_birth }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>Gender:</strong> <span>{{ ucfirst($studentProfile->gender) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>Address:</strong> <span>{{ $studentProfile->address }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Parent Info -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0" style="border-radius:15px;">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Parent / Guardian Information</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>Parent Name:</strong> <span>{{ $studentProfile->parent_name}}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>Phone:</strong> <span>{{ $studentProfile->parent_number}}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>Relationship:</strong> <span>{{ $studentProfile->parent_relationship}}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
@endsection
