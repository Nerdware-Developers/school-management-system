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
                        @if($classTeacher)
                            <p class="mb-0 text-muted">Class Teacher: <strong>{{ $classTeacher->full_name }}</strong></p>
                        @else
                            <p class="mb-0 text-muted">Class Teacher: <span class="text-muted">Not assigned</span></p>
                        @endif
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
                        @php 
                            $creditAvailable = $financialSummary['credit_balance'];
                            // Use the actual closing balance from current term if available
                            $actualBalance = $currentTerm ? $currentTerm->closing_balance : $balance;
                        @endphp
                        @if($actualBalance > 0)
                            <h3 class="fw-bold text-danger">Ksh{{ number_format($actualBalance, 2) }}</h3>
                            <p class="small text-muted mb-0">
                                Updated {{ optional(optional($currentTerm)->updated_at)->diffForHumans() ?? 'N/A' }}
                            </p>
                        @elseif($actualBalance < 0)
                            <h3 class="fw-bold text-success">Credit Ksh{{ number_format(abs($actualBalance), 2) }}</h3>
                            <p class="small text-muted mb-0">Carried into next term</p>
                        @else
                            <h3 class="fw-bold text-success">Settled</h3>
                            <p class="small text-muted mb-0">No outstanding balance</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm" style="background-color:#fff7e6;">
                    <div class="card-body text-center">
                        @if($financialSummary['carried_balance'] > 0)
                            <h6 class="text-muted mb-1">Carried Forward</h6>
                            <h3 class="fw-bold text-warning">Ksh{{ number_format($financialSummary['carried_balance'], 2) }}</h3>
                            <p class="small text-muted mb-0">Prev Balance: Ksh{{ number_format($financialSummary['previous_balance'], 2) }}</p>
                        @elseif($financialSummary['opening_credit'] > 0)
                            <h6 class="text-muted mb-1">Opening Credit</h6>
                            <h3 class="fw-bold text-success">Ksh{{ number_format($financialSummary['opening_credit'], 2) }}</h3>
                            <p class="small text-muted mb-0">Applied to current term</p>
                        @else
                            <h6 class="text-muted mb-1">Previous Term</h6>
                            <h3 class="fw-bold">Ksh{{ number_format(max($financialSummary['previous_balance'], 0), 2) }}</h3>
                            <p class="small text-muted mb-0">Summary overview</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Finance Actions -->
        <div class="row g-4 mb-4">
            <div class="col-lg-12">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">Record Payment</h5>
                            <span class="badge bg-light text-dark">Current Term: {{ optional($currentTerm)->term_name ?? 'N/A' }}</span>
                        </div>
                        @if($currentTerm)
                            @php
                                $termBadge = $currentTerm->closing_balance > 0
                                    ? 'Outstanding: Ksh' . number_format($currentTerm->closing_balance, 2)
                                    : ($currentTerm->closing_balance < 0
                                        ? 'Credit: Ksh' . number_format(abs($currentTerm->closing_balance), 2)
                                        : 'Settled');
                            @endphp
                            <p class="text-muted small mb-3">
                                {{ $termBadge }} |
                                Opening Balance: {{ $currentTerm->opening_balance < 0 ? 'Credit Ksh' . number_format(abs($currentTerm->opening_balance), 2) : 'Ksh' . number_format($currentTerm->opening_balance, 2) }}
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
                                            @if($term->closing_balance > 0)
                                                <span class="fw-bold text-danger">Ksh{{ number_format($term->closing_balance, 2) }}</span>
                                            @elseif($term->closing_balance < 0)
                                                <span class="fw-bold text-success">Credit Ksh{{ number_format(abs($term->closing_balance), 2) }}</span>
                                            @else
                                                <span class="text-muted">Settled</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $badgeClass = match($term->status) {
                                                    'current' => 'bg-primary',
                                                    'carried' => 'bg-warning text-dark',
                                                    'credit' => 'bg-success',
                                                    default => 'bg-secondary',
                                                };
                                                $badgeLabel = $term->status === 'credit' ? 'Credit' : ucfirst($term->status);
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">
                                                {{ $badgeLabel }}
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

        <!-- Exam Results -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Exam Results</h5>
                    @if(!$examResults->isEmpty())
                        <a href="{{ route('report-cards.transcript', $studentProfile->id) }}" 
                           class="btn btn-sm btn-outline-primary" target="_blank">
                            <i class="fas fa-file-alt me-1"></i> Download Full Transcript
                        </a>
                    @endif
                </div>

                @if($examResults->isEmpty())
                    <p class="text-muted mb-0">No exam results found for this student.</p>
                @else
                    <!-- Filter Form -->
                    <form method="GET" action="{{ url('student/profile/' . $studentProfile->id) }}" class="mb-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label for="exam_type" class="form-label">Exam Type</label>
                                <select name="exam_type" id="exam_type" class="form-select">
                                    <option value="">All Exam Types</option>
                                    @foreach($availableExamTypes as $examType)
                                        <option value="{{ $examType }}" {{ $selectedExamType == $examType ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('-', ' ', $examType)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="term" class="form-label">Term</label>
                                <select name="term" id="term" class="form-select">
                                    <option value="">All Terms</option>
                                    @foreach($availableTerms as $term)
                                        <option value="{{ $term }}" {{ $selectedTerm == $term ? 'selected' : '' }}>
                                            {{ $term }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-filter me-1"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>

                    @if($examResults->count() > 0)
                        @php
                            // Get the first (and should be only) result group
                            $results = $examResults->first();
                            $firstResult = $results->first();
                            $exam = $firstResult->exam;
                            $groupKey = $examResults->keys()->first();
                            $groupParts = explode('_', $groupKey);
                            $examType = $groupParts[0] ?? 'Unknown';
                            $term = $groupParts[1] ?? 'Unknown';
                            $className = $groupParts[2] ?? ($exam && $exam->class ? $exam->class->class_name : 'Unknown');
                        @endphp
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold text-primary mb-0">
                                    <i class="fas fa-graduation-cap me-2"></i>
                                    {{ ucfirst(str_replace('-', ' ', $examType)) }} - {{ $term }} 
                                    @if($exam && $exam->class)
                                        <span class="badge bg-secondary ms-2">{{ $className }}</span>
                                    @endif
                                </h6>
                                <a href="{{ route('report-cards.generate', $studentProfile->id) }}?term={{ urlencode($term) }}&exam_type={{ urlencode($examType) }}" 
                                   class="btn btn-sm btn-primary" target="_blank">
                                    <i class="fas fa-download me-1"></i> Download Report Card
                                </a>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Subject</th>
                                            <th>Exam Name</th>
                                            <th>Marks Obtained</th>
                                            <th>Total Marks</th>
                                            <th>Percentage</th>
                                            <th>Grade</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $totalMarks = 0;
                                            $totalPossible = 0;
                                        @endphp
                                        @foreach($results as $result)
                                            @php
                                                $exam = $result->exam;
                                                $marks = $result->marks ?? 0;
                                                $totalMarksPossible = $exam->total_marks ?? 100;
                                                $percentage = $totalMarksPossible > 0 ? ($marks / $totalMarksPossible) * 100 : 0;
                                                $totalMarks += $marks;
                                                $totalPossible += $totalMarksPossible;
                                                
                                                // Calculate grade
                                                $grade = 'F';
                                                if ($percentage >= 90) $grade = 'A+';
                                                elseif ($percentage >= 80) $grade = 'A';
                                                elseif ($percentage >= 70) $grade = 'B+';
                                                elseif ($percentage >= 60) $grade = 'B';
                                                elseif ($percentage >= 50) $grade = 'C+';
                                                elseif ($percentage >= 40) $grade = 'C';
                                                elseif ($percentage >= 30) $grade = 'D';
                                            @endphp
                                            <tr>
                                                <td><strong>{{ $exam->subject ?? 'N/A' }}</strong></td>
                                                <td>{{ $exam->exam_name ?? 'N/A' }}</td>
                                                <td>
                                                    <span class="fw-bold">{{ number_format($marks, 2) }}</span>
                                                </td>
                                                <td>{{ number_format($totalMarksPossible, 2) }}</td>
                                                <td>
                                                    <span class="badge {{ $percentage >= 50 ? 'bg-success' : ($percentage >= 40 ? 'bg-warning' : 'bg-danger') }}">
                                                        {{ number_format($percentage, 1) }}%
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge {{ $grade == 'A+' || $grade == 'A' ? 'bg-success' : ($grade == 'B+' || $grade == 'B' ? 'bg-info' : ($grade == 'C+' || $grade == 'C' ? 'bg-warning' : 'bg-danger')) }}">
                                                        {{ $grade }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($exam && $exam->exam_date)
                                                        {{ \Carbon\Carbon::parse($exam->exam_date)->format('d M Y') }}
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        @php
                                            $overallPercentage = $totalPossible > 0 ? ($totalMarks / $totalPossible) * 100 : 0;
                                            $overallGrade = 'F';
                                            if ($overallPercentage >= 90) $overallGrade = 'A+';
                                            elseif ($overallPercentage >= 80) $overallGrade = 'A';
                                            elseif ($overallPercentage >= 70) $overallGrade = 'B+';
                                            elseif ($overallPercentage >= 60) $overallGrade = 'B';
                                            elseif ($overallPercentage >= 50) $overallGrade = 'C+';
                                            elseif ($overallPercentage >= 40) $overallGrade = 'C';
                                            elseif ($overallPercentage >= 30) $overallGrade = 'D';
                                        @endphp
                                        <tr class="table-info fw-bold">
                                            <td colspan="2"><strong>Total</strong></td>
                                            <td><strong>{{ number_format($totalMarks, 2) }}</strong></td>
                                            <td><strong>{{ number_format($totalPossible, 2) }}</strong></td>
                                            <td>
                                                <span class="badge {{ $overallPercentage >= 50 ? 'bg-success' : ($overallPercentage >= 40 ? 'bg-warning' : 'bg-danger') }}">
                                                    {{ number_format($overallPercentage, 1) }}%
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $overallGrade == 'A+' || $overallGrade == 'A' ? 'bg-success' : ($overallGrade == 'B+' || $overallGrade == 'B' ? 'bg-info' : ($overallGrade == 'C+' || $overallGrade == 'C' ? 'bg-warning' : 'bg-danger')) }}">
                                                    {{ $overallGrade }}
                                                </span>
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <p class="text-muted mb-0">No exam results found for the selected filter.</p>
                    @endif
                @endif
            </div>
        </div>

    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
@endsection
