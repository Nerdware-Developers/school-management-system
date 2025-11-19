
@extends('layouts.master')
@section('content')
    {{-- message --}}
    {!! Toastr::message() !!}
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Add Subject</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="subjects.html">Subject</a></li>
                            <li class="breadcrumb-item active">Add Subject</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('subject.save') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-12">
                                        <h5 class="form-title"><span>Subject Information</span></h5>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group local-forms">
                                            <label>Teacher Name <span class="login-danger">*</span></label>
                                            <select class="form-control @error('teacher_name') is-invalid @enderror" name="teacher_name">
                                                <option value="">Select Teacher</option>
                                                @foreach($teachers as $teacher)
                                                    <option value="{{ $teacher->full_name }}" {{ old('teacher_name') == $teacher->full_name ? 'selected' : '' }}>{{ $teacher->full_name }}</option>
                                                @endforeach
                                            </select>
                                            @error('teacher_name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group local-forms">
                                            <label>Subject <span class="login-danger">*</span></label>
                                            <input type="text" class="form-control @error('subject_name') is-invalid @enderror" name="subject_name" value="{{ old('subject_name') }}" placeholder="Enter Subject Name">
                                            @error('subject_name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                            <div class="form-group local-forms">
                                                <label>Class <span class="login-danger">*</span></label>
                                                <select class="form-control @error('class') is-invalid @enderror" name="class">
                                                    <option value="">Select Class</option>
                                                    @foreach ($classes as $classe)
                                                        <option value="{{ $classe->class_name }}" {{ old('class') == $classe->class_name ? 'selected' : '' }}>{{ $classe->class_name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('class')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                    <div class="col-12">
                                        <div class="student-submit">
                                            <button type="submit" class="btn btn-primary">Submit</button>
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
