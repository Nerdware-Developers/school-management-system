@extends('layouts.master')
@section('content')
{{-- message --}}
{!! Toastr::message() !!}
<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Edit Employer</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('employers.index') }}">Employers</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('employers.show', $employer->id) }}">{{ $employer->full_name }}</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('employers.update', $employer->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="form-title"><span>Basic Details</span></h5>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group local-forms">
                                        <label>Full Name <span class="login-danger">*</span></label>
                                        <input type="text" class="form-control @error('full_name') is-invalid @enderror" 
                                            name="full_name" value="{{ old('full_name', $employer->full_name) }}" placeholder="Enter full name">
                                        @error('full_name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group local-forms">
                                        <label>Employee ID</label>
                                        <input type="text" class="form-control @error('employee_id') is-invalid @enderror"
                                            name="employee_id" value="{{ old('employee_id', $employer->employee_id) }}" readonly>
                                        @error('employee_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group local-forms">
                                        <label>Gender</label>
                                        <select class="form-control select @error('gender') is-invalid @enderror" name="gender">
                                            <option value="">Select Gender</option>
                                            <option value="Female" {{ old('gender', $employer->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                            <option value="Male" {{ old('gender', $employer->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                            <option value="Others" {{ old('gender', $employer->gender) == 'Others' ? 'selected' : '' }}>Others</option>
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
                                        <label>Date Of Birth</label>
                                        <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" 
                                            name="date_of_birth" value="{{ old('date_of_birth', $employer->date_of_birth?->format('Y-m-d')) }}">
                                        @error('date_of_birth')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group local-forms">
                                        <label>Position</label>
                                        <input type="text" class="form-control @error('position') is-invalid @enderror" 
                                            name="position" value="{{ old('position', $employer->position) }}" placeholder="e.g., Administrator, Secretary">
                                        @error('position')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group local-forms">
                                        <label>Department</label>
                                        <input type="text" class="form-control @error('department') is-invalid @enderror" 
                                            name="department" value="{{ old('department', $employer->department) }}" placeholder="e.g., Administration, Support">
                                        @error('department')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group local-forms calendar-icon">
                                        <label>Joining Date</label>
                                        <input type="date" class="form-control @error('joining_date') is-invalid @enderror" 
                                            name="joining_date" value="{{ old('joining_date', $employer->joining_date?->format('Y-m-d')) }}">
                                        @error('joining_date')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <h5 class="form-title"><span>Contact Information</span></h5>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group local-forms">
                                        <label>Phone Number</label>
                                        <input type="text" class="form-control @error('phone_number') is-invalid @enderror" 
                                            name="phone_number" value="{{ old('phone_number', $employer->phone_number) }}" placeholder="Enter phone number">
                                        @error('phone_number')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group local-forms">
                                        <label>Email</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                            name="email" value="{{ old('email', $employer->email) }}" placeholder="Enter email address">
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <h5 class="form-title"><span>Address</span></h5>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group local-forms">
                                        <label>Address</label>
                                        <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                            name="address" value="{{ old('address', $employer->address) }}" placeholder="Enter address">
                                        @error('address')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group local-forms">
                                        <label>City</label>
                                        <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                            name="city" value="{{ old('city', $employer->city) }}" placeholder="Enter city">
                                        @error('city')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group local-forms">
                                        <label>State</label>
                                        <input type="text" class="form-control @error('state') is-invalid @enderror" 
                                            name="state" value="{{ old('state', $employer->state) }}" placeholder="Enter state">
                                        @error('state')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group local-forms">
                                        <label>Zip Code</label>
                                        <input type="text" class="form-control @error('zip_code') is-invalid @enderror" 
                                            name="zip_code" value="{{ old('zip_code', $employer->zip_code) }}" placeholder="Enter zip code">
                                        @error('zip_code')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group local-forms">
                                        <label>Country</label>
                                        <input type="text" class="form-control @error('country') is-invalid @enderror" 
                                            name="country" value="{{ old('country', $employer->country) }}" placeholder="Enter country">
                                        @error('country')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group local-forms">
                                        <label>Monthly Salary (Ksh)</label>
                                        <input type="number" step="0.01" class="form-control @error('monthly_salary') is-invalid @enderror" 
                                               name="monthly_salary" placeholder="0.00" value="{{ old('monthly_salary', $employer->monthly_salary) }}">
                                        @error('monthly_salary')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group local-forms">
                                        <label>Notes</label>
                                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                            name="notes" rows="3" placeholder="Additional notes">{{ old('notes', $employer->notes) }}</textarea>
                                        @error('notes')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="student-submit">
                                        <button type="submit" class="btn btn-primary">Update</button>
                                        <a href="{{ route('employers.show', $employer->id) }}" class="btn btn-secondary">Cancel</a>
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

