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
                    <img src="{{ Storage::url('student-photos/'.$studentProfile->image) }}"
                         alt="Profile Photo"
                         class="rounded-circle shadow-sm"
                         width="100" height="100"
                         style="object-fit:cover;">
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
            <div class="col-md-4">
                <div class="card border-0 shadow-sm" style="background-color:#e0f2fe;">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-1">Fee per Term</h6>
                        <h3 class="fw-bold">Ksh{{ number_format($studentProfile->fee_amount ?? 2850, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm" style="background-color:#dcfce7;">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-1">Amount Paid</h6>
                        <h3 class="fw-bold">Ksh{{ number_format($studentProfile->amount_paid ?? 2500, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm" style="background-color:#fee2e2;">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-1">Balance</h6>
                        <h3 class="fw-bold text-danger">Ksh{{ number_format($studentProfile->balance ?? 350, 2) }}</h3>
                    </div>
                </div>
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
                                <strong>Address:</strong> <span>{{ $studentProfile->address ?? 'Nairobi, Kenya' }}</span>
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
                                <strong>Parent Name:</strong> <span>{{ $studentProfile->parent_name ?? 'John Doe' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>Phone:</strong> <span>{{ $studentProfile->parent_number ?? '+254 712 345 678' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>Relationship:</strong> <span>{{ $studentProfile->parent_relationship ?? 'Father' }}</span>
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
