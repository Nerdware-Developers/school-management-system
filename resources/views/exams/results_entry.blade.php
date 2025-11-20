@extends('layouts.master')
@section('content')
{!! Toastr::message() !!}
<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Enter Exam Results</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('exams.page') }}">Exams</a></li>
                        <li class="breadcrumb-item active">Enter Results</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('exam.results.entry') }}" class="mb-4">
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <label class="form-label">Select Exam <span class="text-danger">*</span></label>
                                    <select name="exam_id" class="form-control" required>
                                        <option value="">-- Select Exam --</option>
                                        @foreach($exams as $exam)
                                            <option value="{{ $exam->id }}" 
                                                {{ $selectedExamId == $exam->id ? 'selected' : '' }}>
                                                {{ $exam->exam_name }} - {{ $exam->term }} ({{ $exam->subject }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label">Select Class <span class="text-danger">*</span></label>
                                    <select name="class_id" class="form-control" required>
                                        <option value="">-- Select Class --</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}" 
                                                {{ $selectedClassId == $class->id ? 'selected' : '' }}>
                                                {{ $class->class_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary w-100">Load Students</button>
                                </div>
                            </div>
                        </form>

                        @if($selectedExamId && $selectedClassId && $students->count() > 0)
                            <form method="POST" action="{{ route('exam.results.save') }}">
                                @csrf
                                <input type="hidden" name="exam_id" value="{{ $selectedExamId }}">
                                <input type="hidden" name="class_id" value="{{ $selectedClassId }}">

                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Admission No</th>
                                                <th>Student Name</th>
                                                <th>Marks</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($students as $student)
                                                @php
                                                    $existingResult = DB::table('exam_results')
                                                        ->where('exam_id', $selectedExamId)
                                                        ->where('student_id', $student->id)
                                                        ->first();
                                                @endphp
                                                <tr>
                                                    <td>{{ $student->admission_number ?? 'N/A' }}</td>
                                                    <td>
                                                        <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>
                                                    </td>
                                                    <td>
                                                        <input type="number" 
                                                               name="marks[{{ $student->id }}]" 
                                                               class="form-control" 
                                                               value="{{ $existingResult->marks ?? '' }}"
                                                               min="0"
                                                               step="0.01"
                                                               placeholder="Enter marks">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Save Results
                                    </button>
                                    <a href="{{ route('exams.page') }}" class="btn btn-secondary">Cancel</a>
                                </div>
                            </form>
                        @elseif($selectedExamId && $selectedClassId && $students->count() == 0)
                            <div class="alert alert-warning">
                                No students found for the selected class.
                            </div>
                        @else
                            <div class="alert alert-info">
                                Please select an exam and class to enter results.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

