
@extends('layouts.master')
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">

            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col-sm-12">
                        <div class="page-sub-header">
                            <h3 class="page-title">Edit Students</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('student/add/page') }}">Student</a></li>
                                <li class="breadcrumb-item active">Edit Students</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            {{-- message --}}
            {!! Toastr::message() !!}
            <div class="row">
                <div class="col-sm-12">
                    <div class="card comman-shadow">
                        <div class="card-body">
                            <form action="{{ route('student/update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" class="form-control" name="id" value="{{ $studentEdit->id }}" readonly>
                                <div class="row">
                                    <div class="col-12">
                                        <h5 class="form-title student-info">Student Information
                                            <span>
                                                <a href="javascript:;"><i class="feather-more-vertical"></i></a>
                                            </span>
                                        </h5>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group local-forms">
                                            <label>First Name <span class="login-danger">*</span></label>
                                            <input type="text" class="form-control @error('first_name') is-invalid @enderror" name="first_name" value="{{ $studentEdit->first_name }}">
                                            @error('first_name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group local-forms">
                                            <label>Last Name <span class="login-danger">*</span></label>
                                            <input type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" value="{{ $studentEdit->last_name }}">
                                            @error('last_name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group local-forms">
                                            <label>Gender <span class="login-danger">*</span></label>
                                            <select class="form-control select  @error('gender') is-invalid @enderror" name="gender">
                                                <option selected disabled>Select Gender</option>
                                                <option value="Female" {{ $studentEdit->gender == 'Female' ? "selected" :"Female"}}>Female</option>
                                                <option value="Male" {{ $studentEdit->gender == 'Male' ? "selected" :""}}>Male</option>
                                                <option value="Others" {{ $studentEdit->gender == 'Others' ? "selected" :""}}>Others</option>
                                            </select>
                                            @error('gender')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group local-forms calendar-icon">
                                            <label>Date Of Birth <span class="login-danger">*</span></label>
                                            <input class="form-control @error('date_of_birth') is-invalid @enderror" name="date_of_birth" type="text"  value="{{ $studentEdit->date_of_birth }}">
                                            @error('date_of_birth')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group local-forms">
                                            <label>Roll </label>
                                            <input class="form-control @error('roll') is-invalid @enderror" type="text" name="roll" value="{{ $studentEdit->roll }}">
                                            @error('roll')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group local-forms">
                                            <label>Blood Group <span class="login-danger">*</span></label>
                                            <input class="form-control @error('blood_group') is-invalid @enderror" type="text" name="blood_group" value="{{ $studentEdit->blood_group }}">
                                            @error('blood_group')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group local-forms">
                                            <label>E-Mail <span class="login-danger">*</span></label>
                                            <input class="form-control @error('parent_email') is-invalid @enderror" type="text" name="parent_email"  value="{{ $studentEdit->parent_email }}">
                                            @error('parent_email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group local-forms">
                                            <label>Class <span class="login-danger">*</span></label>
                                            <input class="form-control @error('class') is-invalid @enderror" type="text" name="class"  value="{{ $studentEdit->class }}">
                                            @error('class')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group local-forms">
                                            <label>Admission ID </label>
                                            <input class="form-control @error('admission_number') is-invalid @enderror" type="text" name="admission_number" value="{{ $studentEdit->admission_number }}">
                                            @error('admission_number')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group local-forms">
                                            <label>Phone </label>
                                            <input class="form-control @error('parent_number') is-invalid @enderror" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1').replace(/^0[^.]/, '0');" name="parent_number" value="{{ $studentEdit->parent_number }}">
                                            @error('parent_number')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group local-forms">
                                            <label>Parent's Name <span class="login-danger">*</span></label>
                                            <input type="text" class="form-control @error('parent_name') is-invalid @enderror" name="parent_name" value="{{ $studentEdit->parent_name }}">
                                            @error('parent_name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group local-forms">
                                            <label>Guardian's Name <span class="login-danger">*</span></label>
                                            <input type="text" class="form-control @error('guardian_name') is-invalid @enderror" name="guardian_name" value="{{ $studentEdit->guardian_name }}">
                                            @error('guardian_name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group local-forms">
                                            <label>Guardian Number <span class="login-danger">*</span></label>
                                            <input type="text" class="form-control @error('guardian_number') is-invalid @enderror" name="guardian_number" value="{{ $studentEdit->guardian_number }}">
                                            @error('guardian_number')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group local-forms">
                                            <label>Address <span class="login-danger">*</span></label>
                                            <input type="text" class="form-control @error('address') is-invalid @enderror" name="address" value="{{ $studentEdit->address }}">
                                            @error('address')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group students-up-files">
                                            <label>Upload Student Photo (150px X 150px)</label>
                                            <div class="uplod">
                                                <h2 class="table-avatar">
                                                    <a class="avatar avatar-sm me-2">
                                                        <img class="avatar-img rounded-circle" src="{{ $studentEdit->image ? route('student.photo', $studentEdit->image) : asset('images/photo_defaults.jpg') }}" alt="">
                                                    </a>
                                                </h2>
                                                <label class="file-upload image-upbtn mb-0 @error('image') is-invalid @enderror">
                                                    Choose File <input type="file" name="image">
                                                </label>
                                                <input type="hidden" name="image_hidden" value="{{ $studentEdit->image }}">
                                                @error('image')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="student-submit">
                                            <button type="submit" class="btn btn-primary">Update</button>
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
