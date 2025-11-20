@extends('layouts.master')
@section('content')
{!! Toastr::message() !!}
<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Report Cards</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Report Cards</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Filter Form -->
                        <form method="GET" action="{{ route('report-cards.index') }}" class="mb-4">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Class</label>
                                    <select name="class_id" class="form-control" onchange="this.form.submit()">
                                        <option value="">-- Select Class --</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}" {{ $selectedClassId == $class->id ? 'selected' : '' }}>
                                                {{ $class->class_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Term</label>
                                    <select name="term" class="form-control" onchange="this.form.submit()">
                                        <option value="">-- Select Term --</option>
                                        @foreach($terms as $term)
                                            <option value="{{ $term }}" {{ $selectedTerm == $term ? 'selected' : '' }}>
                                                {{ $term }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Exam Type</label>
                                    <select name="exam_type" class="form-control" onchange="this.form.submit()">
                                        <option value="">-- Select Type --</option>
                                        @foreach($examTypes as $key => $label)
                                            <option value="{{ $key }}" {{ $selectedExamType == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </form>

                        @if($students->count() > 0 && $selectedTerm && $selectedExamType)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Admission No</th>
                                            <th>Student Name</th>
                                            <th>Class</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($students as $student)
                                            <tr>
                                                <td>{{ $student->admission_number ?? 'N/A' }}</td>
                                                <td><strong>{{ $student->first_name }} {{ $student->last_name }}</strong></td>
                                                <td>{{ $student->class }}</td>
                                                <td class="text-center">
                                                    <a href="{{ route('report-cards.generate', $student->id) }}?term={{ $selectedTerm }}&exam_type={{ $selectedExamType }}" 
                                                       class="btn btn-primary btn-sm me-2" target="_blank">
                                                        <i class="fas fa-file-pdf me-2"></i>Generate Report Card
                                                    </a>
                                                    <a href="{{ route('report-cards.transcript', $student->id) }}" 
                                                       class="btn btn-info btn-sm" target="_blank">
                                                        <i class="fas fa-file-alt me-2"></i>Transcript
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @elseif($selectedClassId)
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Please select term and exam type to generate report cards.
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Please select a class to generate report cards.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

