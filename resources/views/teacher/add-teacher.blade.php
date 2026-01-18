
@extends('layouts.master')
@section('content')
{{-- message --}}
{!! Toastr::message() !!}
<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Add Teachers</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="teachers.html">Teachers</a></li>
                        <li class="breadcrumb-item active">Add Teachers</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('teacher/save') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="form-title"><span>Basic Details</span></h5>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group local-forms">
                                        <label>Full Name <span class="login-danger">*</span></label>
                                        <input type="text" class="form-control @error('full_name') is-invalid @enderror" 
                                            name="full_name" value="{{ old('full_name') }}" placeholder="Enter full name">
                                        @error('full_name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group local-forms">
                                        <label>Teacher ID <span class="login-danger">*</span></label>
                                        <input type="text" class="form-control @error('teacher_id') is-invalid @enderror"
                                            name="teacher_id" value="{{ old('teacher_id', 'TCH-' . rand(1000,9999)) }}" placeholder="Enter teacher ID">
                                        @error('teacher_id')
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
                                            <option value="Female" {{ old('gender') == 'Female' ? "selected" :"Female"}}>Female</option>
                                            <option value="Male" {{ old('gender') == 'Male' ? "selected" :""}}>Male</option>
                                            <option value="Others" {{ old('gender') == 'Others' ? "selected" :""}}>Others</option>
                                        </select>
                                        @error('gender')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group local-forms">
                                        <label>Experience <span class="login-danger">*</span></label>
                                        <input type="text" class="form-control @error('experience') is-invalid @enderror" name="experience" placeholder="Enter Experience" value="{{ old('experience') }}">
                                        @error('experience')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group local-forms calendar-icon">
                                        <label>Qualification <span class="login-danger">*</span></label>
                                        <input type="text" class="form-control datetimepicker @error('qualification') is-invalid @enderror" name="qualification" placeholder="DD-MM-YYYY" value="{{ old('qualification') }}">
                                        @error('qualification')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group local-forms calendar-icon">
                                        <label>Date Of Birth <span class="login-danger">*</span></label>
                                        <input type="text" class="form-control datetimepicker @error('date_of_birth') is-invalid @enderror" name="date_of_birth" placeholder="DD-MM-YYYY" value="{{ old('date_of_birth') }}">
                                        @error('date_of_birth')
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
                                        <label>Address <span class="login-danger">*</span></label>
                                        <input type="text" class="form-control @error('address') is-invalid @enderror" name="address" placeholder="Enter address" value="{{ old('address') }}">
                                        @error('address')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group local-forms">
                                        <label>Phone </label>
                                        <input class="form-control @error('phone_number') is-invalid @enderror" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1').replace(/^0[^.]/, '0');" name="phone_number" placeholder="Enter Phone Number" value="{{ old('phone_number') }}">
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
                                        <input type="text" class="form-control @error('city') is-invalid @enderror" name="city" placeholder="Enter City" value="{{ old('city') }}">
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
                                        <input type="text" class="form-control @error('state') is-invalid @enderror" name="state" placeholder="Enter State" value="{{ old('state') }}">
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
                                        <input type="text" class="form-control @error('zip_code') is-invalid @enderror" name="zip_code" placeholder="Enter Zip" value="{{ old('zip_code') }}">
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
                                        <input type="text" class="form-control @error('country') is-invalid @enderror" name="country" placeholder="Enter Country" value="{{ old('country') }}">
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
                                               name="monthly_salary" placeholder="0.00" value="{{ old('monthly_salary') }}">
                                        @error('monthly_salary')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <h5 class="form-title"><span>Class Teacher Information</span></h5>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group local-forms">
                                        <label>Is Class Teacher?</label>
                                        <select class="form-control select" name="is_class_teacher" id="is_class_teacher">
                                            <option value="no" {{ old('is_class_teacher') == 'no' ? 'selected' : '' }}>No</option>
                                            <option value="yes" {{ old('is_class_teacher') == 'yes' ? 'selected' : '' }}>Yes</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6" id="class_teacher_section" style="display: none;">
                                    <div class="form-group local-forms">
                                        <label>Class <span class="login-danger">*</span></label>
                                        <select class="form-control select @error('class_teacher_id') is-invalid @enderror" name="class_teacher_id" id="class_teacher_id">
                                            <option value="">Select Class</option>
                                            @foreach($classes as $class)
                                                <option value="{{ $class->id }}" {{ old('class_teacher_id') == $class->id ? 'selected' : '' }}>
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

                                <div class="col-12">
                                    <h5 class="form-title"><span>Subject & Class Assignments (Optional)</span></h5>
                                    <p class="text-muted small">You can assign subjects to this teacher later if needed.</p>
                                </div>
                                <div class="col-12">
                                    <div id="subject_class_container">
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

    // select auto teacher id
    $('#full_name').on('change',function()
    {
        $('#teacher_id').val($(this).find(':selected').data('teacher_id'));
    });

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

    // Add new subject-class row
    let rowCount = 1;
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
                                ${classOptions}
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
        
        // Show remove buttons if there's more than one row
        updateRemoveButtons();
    });

    // Remove subject-class row
    $(document).on('click', '.remove-row', function() {
        $(this).closest('.subject-class-row').remove();
        updateRemoveButtons();
    });

    // Update remove buttons visibility
    function updateRemoveButtons() {
        const rows = $('.subject-class-row');
        if (rows.length > 1) {
            $('.remove-row').show();
        } else {
            $('.remove-row').hide();
        }
    }

    // Initialize remove buttons on page load
    updateRemoveButtons();

    // Handle "Add New Subject" option
    $(document).on('change', '.subject-select', function() {
        const rowIndex = $(this).data('row');
        const $row = $(this).closest('.subject-class-row');
        const $newSubjectInput = $row.find('.new-subject-input');
        const $newSubjectClassInput = $row.find('.new-subject-class-input');
        const $existingClassSelect = $row.find('.class-select');
        const $subjectSelect = $(this);
        
        if ($(this).val() === '__new__') {
            // Show new subject inputs
            $newSubjectInput.show().prop('required', true);
            $newSubjectClassInput.show().prop('required', true);
            // Hide existing class select
            $existingClassSelect.hide().prop('required', false);
            $subjectSelect.prop('required', false);
        } else {
            // Hide new subject inputs
            $newSubjectInput.hide().prop('required', false).val('');
            $newSubjectClassInput.hide().prop('required', false).val('');
            // Show existing class select
            $existingClassSelect.show().prop('required', false);
            $subjectSelect.prop('required', false);
        }
    });
</script>
@endsection
@endsection
