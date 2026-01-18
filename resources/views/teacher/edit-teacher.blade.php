
@extends('layouts.master')
@section('content')
{{-- message --}}
{!! Toastr::message() !!}
<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Edit Teachers</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('teacher/list/page') }}">Teachers</a></li>
                        <li class="breadcrumb-item active">Edit Teachers</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('teacher/update') }}" method="POST">
                            @csrf
                            <input type="hidden" class="form-control" name="id" value="{{ $teacher->id }}">
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="form-title"><span>Basic Details</span></h5>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group local-forms">
                                        <label>Name <span class="login-danger">*</span></label>
                                        <input type="text" class="form-control @error('full_name') is-invalid @enderror" name="full_name" placeholder="Enter Name" value="{{ $teacher->full_name }}">
                                        @error('full_name')
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
                                            <option value="Female" {{ $teacher->gender == 'Female' ? "selected" :"Female"}}>Female</option>
                                            <option value="Male" {{ $teacher->gender == 'Male' ? "selected" :""}}>Male</option>
                                            <option value="Others" {{ $teacher->gender == 'Others' ? "selected" :""}}>Others</option>
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
                                        <input type="text" class="form-control datetimepicker @error('date_of_birth') is-invalid @enderror" name="date_of_birth" placeholder="DD-MM-YYYY" value="{{ $teacher->date_of_birth }}">
                                        @error('date_of_birth')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group local-forms calendar-icon">
                                        <label>Joining Date <span class="login-danger">*</span></label>
                                        <input type="text" class="form-control @error('joining_date') is-invalid @enderror" name="joining_date" value="{{ $teacher->join_date}}" readonly>
                                        @error('joining_date')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4 local-forms">
                                    <div class="form-group local-forms calendar-icon">
                                        <label>Qualification <span class="login-danger">*</span></label>
                                        <input type="text" class="form-control datetimepicker @error('qualification') is-invalid @enderror" name="qualification" placeholder="Enter Joining Date" value="{{ $teacher->qualification }}">
                                        @error('qualification')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group local-forms">
                                        <label>Experience <span class="login-danger">*</span></label>
                                        <input type="text" class="form-control @error('experience') is-invalid @enderror" name="experience" placeholder="Enter Experience" value="{{ $teacher->experience }}">
                                        @error('experience')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <h5 class="form-title"><span>Address</span></h5>
                                </div>
                                <div class="col-6">
                                    <div class="form-group local-forms">
                                        <label>Address <span class="login-danger">*</span></label>
                                        <input type="text" class="form-control @error('address') is-invalid @enderror" name="address" placeholder="Enter address" value="{{ $teacher->address }}">
                                        @error('address')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group local-forms">
                                        <label>Phone <span class="login-danger">*</span></label>
                                        <input type="text" class="form-control @error('phone_number') is-invalid @enderror" name="phone_number" placeholder="Enter phone number" value="{{ $teacher->phone_number }}">
                                        @error('phone_number')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group local-forms">
                                        <label>City <span class="login-danger">*</span></label>
                                        <input type="text" class="form-control @error('city') is-invalid @enderror" name="city" placeholder="Enter City" value="{{ $teacher->city }}">
                                        @error('city')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group local-forms">
                                        <label>State <span class="login-danger">*</span></label>
                                        <input type="text" class="form-control @error('state') is-invalid @enderror" name="state" placeholder="Enter State" value="{{ $teacher->state }}">
                                        @error('state')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group local-forms">
                                        <label>Zip Code <span class="login-danger">*</span></label>
                                        <input type="text" class="form-control @error('zip_code') is-invalid @enderror" name="zip_code" placeholder="Enter Zip" value="{{ $teacher->zip_code }}">
                                        @error('zip_code')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group local-forms">
                                        <label>Country <span class="login-danger">*</span></label>
                                        <input type="text" class="form-control @error('country') is-invalid @enderror" name="country" placeholder="Enter Country" value="{{ $teacher->country }}">
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
                                               name="monthly_salary" placeholder="0.00" value="{{ old('monthly_salary', $teacher->monthly_salary) }}">
                                        @error('monthly_salary')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Class Teacher Section -->
                                <div class="col-12">
                                    <h5 class="form-title"><span>Class Teacher Role</span></h5>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group local-forms">
                                        <label>Is Class Teacher?</label>
                                        <select class="form-control select @error('is_class_teacher') is-invalid @enderror" name="is_class_teacher" id="is_class_teacher">
                                            <option value="no" {{ old('is_class_teacher', $teacher->class_teacher_id ? 'yes' : 'no') == 'no' ? 'selected' : '' }}>No</option>
                                            <option value="yes" {{ old('is_class_teacher', $teacher->class_teacher_id ? 'yes' : 'no') == 'yes' ? 'selected' : '' }}>Yes</option>
                                        </select>
                                        @error('is_class_teacher')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6" id="class_teacher_section" style="{{ old('is_class_teacher', $teacher->class_teacher_id ? 'yes' : 'no') == 'yes' ? '' : 'display: none;' }}">
                                    <div class="form-group local-forms">
                                        <label>Class <span class="login-danger">*</span></label>
                                        <select class="form-control select @error('class_teacher_id') is-invalid @enderror" name="class_teacher_id" id="class_teacher_id">
                                            <option value="">Select Class</option>
                                            @foreach($classes as $class)
                                                <option value="{{ $class->id }}" {{ old('class_teacher_id', $teacher->class_teacher_id) == $class->id ? 'selected' : '' }}>
                                                    {{ $class->class_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('class_teacher_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Subject & Class Assignments -->
                                <div class="col-12">
                                    <h5 class="form-title"><span>Subject & Class Assignments</span></h5>
                                    <p class="text-muted small">Assign subjects and classes this teacher teaches.</p>
                                </div>
                                <div class="col-12">
                                    <div id="subject_class_container">
                                        @php
                                            $assignments = $teacher->teachingAssignments;
                                            $rowIndex = 0;
                                        @endphp
                                        @if($assignments->count() > 0)
                                            @foreach($assignments as $assignment)
                                                <div class="subject-class-row mb-3" style="border: 1px solid #ddd; padding: 15px; border-radius: 5px;">
                                                    <div class="row">
                                                        <div class="col-12 col-sm-5">
                                                            <div class="form-group local-forms">
                                                                <label>Subject</label>
                                                                <select class="form-control select subject-select" name="subject_class[{{ $rowIndex }}][subject_id]" data-row="{{ $rowIndex }}">
                                                                    <option value="">Select Subject (Optional)</option>
                                                                    <option value="__new__">+ Add New Subject</option>
                                                                    @foreach($subjects as $subject)
                                                                        <option value="{{ $subject->id }}" {{ $assignment->subject_id == $subject->id ? 'selected' : '' }}>
                                                                            {{ $subject->subject_name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                <input type="text" class="form-control mt-2 new-subject-input" name="subject_class[{{ $rowIndex }}][new_subject_name]" placeholder="Enter new subject name" style="display: none;">
                                                                <select class="form-control mt-2 new-subject-class-input" name="subject_class[{{ $rowIndex }}][new_subject_class]" style="display: none;">
                                                                    <option value="">Select Class for New Subject</option>
                                                                    @foreach($classes as $class)
                                                                        <option value="{{ $class->class_name }}">{{ $class->class_name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-sm-5">
                                                            <div class="form-group local-forms">
                                                                <label>Class</label>
                                                                <select class="form-control select class-select" name="subject_class[{{ $rowIndex }}][class_id]">
                                                                    <option value="">Select Class (Optional)</option>
                                                                    @foreach($classes as $class)
                                                                        <option value="{{ $class->id }}" {{ $assignment->class_id == $class->id ? 'selected' : '' }}>
                                                                            {{ $class->class_name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-sm-2">
                                                            <div class="form-group local-forms">
                                                                <label>&nbsp;</label>
                                                                <button type="button" class="btn btn-danger btn-block remove-row">{{ $assignments->count() > 1 ? 'Remove' : '' }}</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @php $rowIndex++; @endphp
                                            @endforeach
                                        @else
                                            <div class="subject-class-row mb-3" style="border: 1px solid #ddd; padding: 15px; border-radius: 5px;">
                                                <div class="row">
                                                    <div class="col-12 col-sm-5">
                                                        <div class="form-group local-forms">
                                                            <label>Subject</label>
                                                            <select class="form-control select subject-select" name="subject_class[0][subject_id]" data-row="0">
                                                                <option value="">Select Subject (Optional)</option>
                                                                <option value="__new__">+ Add New Subject</option>
                                                                @foreach($subjects as $subject)
                                                                    <option value="{{ $subject->id }}">{{ $subject->subject_name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <input type="text" class="form-control mt-2 new-subject-input" name="subject_class[0][new_subject_name]" placeholder="Enter new subject name" style="display: none;">
                                                            <select class="form-control mt-2 new-subject-class-input" name="subject_class[0][new_subject_class]" style="display: none;">
                                                                <option value="">Select Class for New Subject</option>
                                                                @foreach($classes as $class)
                                                                    <option value="{{ $class->class_name }}">{{ $class->class_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-sm-5">
                                                        <div class="form-group local-forms">
                                                            <label>Class</label>
                                                            <select class="form-control select class-select" name="subject_class[0][class_id]">
                                                                <option value="">Select Class (Optional)</option>
                                                                @foreach($classes as $class)
                                                                    <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-sm-2">
                                                        <div class="form-group local-forms">
                                                            <label>&nbsp;</label>
                                                            <button type="button" class="btn btn-danger btn-block remove-row" style="display: none;">Remove</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <button type="button" class="btn btn-success" id="add_subject_class_row">
                                        <i class="fas fa-plus"></i> Add Another Subject-Class
                                    </button>
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
@section('script')
<script>
    // Prepare subject and class options for dynamic rows
    const subjectOptions = `@foreach($subjects as $subject)<option value="{{ $subject->id }}">{{ $subject->subject_name }}</option>@endforeach`;
    const classOptions = `@foreach($classes as $class)<option value="{{ $class->id }}">{{ $class->class_name }}</option>@endforeach`;
    const classNameOptions = `@foreach($classes as $class)<option value="{{ $class->class_name }}">{{ $class->class_name }}</option>@endforeach`;

    // Show/hide class teacher section
    $('#is_class_teacher').on('change', function() {
        if ($(this).val() === 'yes') {
            $('#class_teacher_section').show();
            $('#class_teacher_id').prop('required', true);
        } else {
            $('#class_teacher_section').hide();
            $('#class_teacher_id').prop('required', false);
            $('#class_teacher_id').val('');
        }
    });

    // Trigger on page load if value is already set
    if ($('#is_class_teacher').val() === 'yes') {
        $('#class_teacher_section').show();
        $('#class_teacher_id').prop('required', true);
    }

    // Handle subject selection for new subject
    $(document).on('change', '.subject-select', function() {
        var $row = $(this).closest('.subject-class-row');
        var $newSubjectInput = $row.find('.new-subject-input');
        var $newSubjectClassInput = $row.find('.new-subject-class-input');
        
        if ($(this).val() === '__new__') {
            $newSubjectInput.show();
            $newSubjectClassInput.show();
        } else {
            $newSubjectInput.hide().val('');
            $newSubjectClassInput.hide().val('');
        }
        
        // Show/hide remove button
        var rowCount = $('.subject-class-row').length;
        $row.find('.remove-row').toggle(rowCount > 1);
    });

    // Add new subject-class row
    let rowCount = {{ $teacher->teachingAssignments->count() > 0 ? $teacher->teachingAssignments->count() : 1 }};
    $('#add_subject_class_row').on('click', function() {
        const newRow = `
            <div class="subject-class-row mb-3" style="border: 1px solid #ddd; padding: 15px; border-radius: 5px;">
                <div class="row">
                    <div class="col-12 col-sm-5">
                        <div class="form-group local-forms">
                            <label>Subject</label>
                            <select class="form-control select subject-select" name="subject_class[${rowCount}][subject_id]" data-row="${rowCount}">
                                <option value="">Select Subject (Optional)</option>
                                <option value="__new__">+ Add New Subject</option>
                                ${subjectOptions}
                            </select>
                            <input type="text" class="form-control mt-2 new-subject-input" name="subject_class[${rowCount}][new_subject_name]" placeholder="Enter new subject name" style="display: none;">
                            <select class="form-control mt-2 new-subject-class-input" name="subject_class[${rowCount}][new_subject_class]" style="display: none;">
                                <option value="">Select Class for New Subject</option>
                                ${classNameOptions}
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-sm-5">
                        <div class="form-group local-forms">
                            <label>Class</label>
                            <select class="form-control select class-select" name="subject_class[${rowCount}][class_id]">
                                <option value="">Select Class (Optional)</option>
                                ${classOptions}
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-sm-2">
                        <div class="form-group local-forms">
                            <label>&nbsp;</label>
                            <button type="button" class="btn btn-danger btn-block remove-row">Remove</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        $('#subject_class_container').append(newRow);
        rowCount++;
        
        // Update remove button visibility
        $('.subject-class-row').each(function() {
            var count = $('.subject-class-row').length;
            $(this).find('.remove-row').toggle(count > 1);
        });
    });

    // Remove subject-class row
    $(document).on('click', '.remove-row', function() {
        var count = $('.subject-class-row').length;
        if (count > 1) {
            $(this).closest('.subject-class-row').remove();
            // Update remove button visibility
            $('.subject-class-row').each(function() {
                var newCount = $('.subject-class-row').length;
                $(this).find('.remove-row').toggle(newCount > 1);
            });
        }
    });

    // Initialize remove button visibility
    $(document).ready(function() {
        var count = $('.subject-class-row').length;
        $('.subject-class-row').each(function() {
            $(this).find('.remove-row').toggle(count > 1);
        });
        
        // Handle existing rows with __new__ selected
        $('.subject-select').each(function() {
            if ($(this).val() === '__new__') {
                var $row = $(this).closest('.subject-class-row');
                $row.find('.new-subject-input').show();
                $row.find('.new-subject-class-input').show();
            }
        });
    });
</script>
@endsection
