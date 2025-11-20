@extends('layouts.master')
@section('content')
{!! Toastr::message() !!}
<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Create Exam</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('exams.page') }}">Exams</a></li>
                        <li class="breadcrumb-item active">Create Exam</li>
                    </ul>
                </div>
                <div class="col-auto">
                    <a href="{{ route('exams.page') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Exams
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Create exams for all subjects:</strong> This will create exams for all subjects in the selected class. 
                            You can then enter marks for all students and all subjects at once.
                        </div>

                        <form action="{{ route('exam.create') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group local-forms">
                                        <label>Exam Type <span class="login-danger">*</span></label>
                                        <select name="exam_type" class="form-control @error('exam_type') is-invalid @enderror" required>
                                            <option value="">-- Select Exam Type --</option>
                                            <option value="mid-term" {{ old('exam_type') == 'mid-term' ? 'selected' : '' }}>Mid-Term</option>
                                            <option value="end-term" {{ old('exam_type') == 'end-term' ? 'selected' : '' }}>End-Term</option>
                                        </select>
                                        @error('exam_type')
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
                                        <label>Class <span class="login-danger">*</span></label>
                                        <select name="class_id" class="form-control @error('class_id') is-invalid @enderror" required>
                                            <option value="">-- Select Class --</option>
                                            @foreach($classes as $class)
                                                <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                                    {{ $class->class_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('class_id')
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
                                    <div class="student-submit">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i>Create Exams for All Subjects
                                        </button>
                                        <a href="{{ route('exams.page') }}" class="btn btn-secondary">Cancel</a>
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
