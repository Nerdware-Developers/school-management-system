@extends('layouts.master')
@section('content')
{!! Toastr::message() !!}
<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Edit Exam</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('exams.page') }}">Exams</a></li>
                        <li class="breadcrumb-item active">Edit Exam</li>
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
                        <form action="{{ route('exam.update', $exam->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group local-forms">
                                        <label>Exam Name <span class="login-danger">*</span></label>
                                        <input type="text" name="exam_name" 
                                               class="form-control @error('exam_name') is-invalid @enderror" 
                                               value="{{ old('exam_name', $exam->exam_name) }}" required>
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
                                            <option value="Term 1" {{ old('term', $exam->term) == 'Term 1' ? 'selected' : '' }}>Term 1</option>
                                            <option value="Term 2" {{ old('term', $exam->term) == 'Term 2' ? 'selected' : '' }}>Term 2</option>
                                            <option value="Term 3" {{ old('term', $exam->term) == 'Term 3' ? 'selected' : '' }}>Term 3</option>
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
                                        <label>Exam Type</label>
                                        <select name="exam_type" class="form-control @error('exam_type') is-invalid @enderror">
                                            <option value="">-- Select Type --</option>
                                            <option value="mid-term" {{ old('exam_type', $exam->exam_type) == 'mid-term' ? 'selected' : '' }}>Mid-Term</option>
                                            <option value="end-term" {{ old('exam_type', $exam->exam_type) == 'end-term' ? 'selected' : '' }}>End-Term</option>
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
                                        <label>Class <span class="login-danger">*</span></label>
                                        <select name="class_id" class="form-control @error('class_id') is-invalid @enderror" required>
                                            <option value="">-- Select Class --</option>
                                            @foreach($classes as $class)
                                                <option value="{{ $class->id }}" 
                                                    {{ old('class_id', $exam->class_id) == $class->id ? 'selected' : '' }}>
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
                                    <div class="form-group local-forms">
                                        <label>Subject <span class="login-danger">*</span></label>
                                        <input type="text" name="subject" 
                                               class="form-control @error('subject') is-invalid @enderror" 
                                               value="{{ old('subject', $exam->subject) }}" required>
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
                                               value="{{ old('total_marks', $exam->total_marks) }}">
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
                                               value="{{ old('exam_date', $exam->exam_date ? \Carbon\Carbon::parse($exam->exam_date)->format('Y-m-d') : '') }}">
                                        @error('exam_date')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="student-submit">
                                        <button type="submit" class="btn btn-primary">Update Exam</button>
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

