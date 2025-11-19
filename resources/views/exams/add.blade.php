@extends('layouts.master')
@section('content')
{!! Toastr::message() !!}
<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Add Exam</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Exams</a></li>
                        <li class="breadcrumb-item active">Add Exam</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('exam.save') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group local-forms">
                                        <label>Exam Name <span class="login-danger">*</span></label>
                                        <input type="text" name="exam_name" class="form-control" required>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <div class="form-group local-forms">
                                        <label>Term <span class="login-danger">*</span></label>
                                        <select name="term" class="form-control" required>
                                            <option value="">-- Select Term --</option>
                                            <option value="Term 1">Term 1</option>
                                            <option value="Term 2">Term 2</option>
                                            <option value="Term 3">Term 3</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <div class="form-group local-forms">
                                        <label>Class <span class="login-danger">*</span></label>
                                        <select name="class_id" class="form-control" required>
                                            <option value="">-- Select Class --</option>
                                            @foreach($classes as $class)
                                                <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <div class="form-group local-forms">
                                        <label>Subject <span class="login-danger">*</span></label>
                                        <input type="text" name="subject" class="form-control" required>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <div class="form-group local-forms">
                                        <label>Total Marks</label>
                                        <input type="number" name="total_marks" class="form-control" value="100">
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <div class="form-group local-forms calendar-icon">
                                        <label>Exam Date</label>
                                        <input type="text" name="exam_date" class="form-control datetimepicker" placeholder="DD-MM-YYYY">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="student-submit">
                                        <button type="submit" class="btn btn-primary">Save Exam</button>
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
