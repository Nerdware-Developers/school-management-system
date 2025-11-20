@extends('layouts.master')
@section('content')
{!! Toastr::message() !!}
<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Add Exam for {{ $student->first_name }} {{ $student->last_name }}</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('student/profile', $student->id) }}">Student Profile</a></li>
                        <li class="breadcrumb-item active">Add Exam</li>
                    </ul>
                </div>
                <div class="col-auto">
                    <a href="{{ route('student/profile', $student->id) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Profile
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('exam.save-for-student', $student->id) }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group local-forms">
                                        <label>Exam Name <span class="login-danger">*</span></label>
                                        <input type="text" name="exam_name" 
                                               class="form-control @error('exam_name') is-invalid @enderror" 
                                               value="{{ old('exam_name') }}" required>
                                        @error('exam_name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <div class="form-group local-forms">
                                        <label>Term <span class="login-danger">*</span></label>
                                        <select name="term" class="form-control @error('term') is-invalid @enderror" required>
                                            <option value="">-- Select Term --</option>
                                            <option value="Term 1" {{ old('term') == 'Term 1' ? 'selected' : '' }}>Term 1</option>
                                            <option value="Term 2" {{ old('term') == 'Term 2' ? 'selected' : '' }}>Term 2</option>
                                            <option value="Term 3" {{ old('term') == 'Term 3' ? 'selected' : '' }}>Term 3</option>
                                        </select>
                                        @error('term')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <div class="form-group local-forms">
                                        <label>Subject <span class="login-danger">*</span></label>
                                        <input type="text" name="subject" 
                                               class="form-control @error('subject') is-invalid @enderror" 
                                               value="{{ old('subject') }}" required>
                                        @error('subject')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <div class="form-group local-forms">
                                        <label>Total Marks</label>
                                        <input type="number" name="total_marks" 
                                               class="form-control @error('total_marks') is-invalid @enderror" 
                                               value="{{ old('total_marks', 100) }}">
                                        @error('total_marks')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <div class="form-group local-forms calendar-icon">
                                        <label>Exam Date</label>
                                        <input type="date" name="exam_date" 
                                               class="form-control @error('exam_date') is-invalid @enderror" 
                                               value="{{ old('exam_date') }}">
                                        @error('exam_date')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        This exam will be assigned specifically to <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>.
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="student-submit">
                                        <button type="submit" class="btn btn-primary">Save Exam</button>
                                        <a href="{{ route('student/profile', $student->id) }}" class="btn btn-secondary">Cancel</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

