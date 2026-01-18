
@extends('layouts.master')
@section('content')
    {{-- message --}}
    {!! Toastr::message() !!}
    <style>
        .subject-checkbox:checked + label {
            background-color: #e3f2fd;
        }
        tr:has(.subject-checkbox:checked) {
            background-color: #e3f2fd !important;
        }
        #bulkDeleteBtn {
            transition: all 0.3s ease;
        }
    </style>
    <div class="page-wrapper">
        <div class="content container-fluid">

            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Subjects</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                            <li class="breadcrumb-item active">Subjects</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="student-group-form">
                <form method="GET" action="{{ route('subject/list/page') }}">
                    <div class="row">
                        <div class="col-lg-3 col-md-6">
                            <div class="form-group">
                                <input type="text" class="form-control" name="id" placeholder="Search by ID ..." value="{{ request('id') }}">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="form-group">
                                <input type="text" class="form-control" name="name" placeholder="Search by Name ..." value="{{ request('name') }}">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="form-group">
                                <input type="text" class="form-control" name="class" placeholder="Search by Class ..." value="{{ request('class') }}">
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="search-student-btn">
                                <button type="submit" class="btn btn-primary">Search</button>
                                @if(request()->hasAny(['id', 'name', 'class']))
                                    <a href="{{ route('subject/list/page') }}" class="btn btn-secondary mt-2" style="display: block; width: 100%;">Clear</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card card-table">
                        <div class="card-body">
                            <div class="page-header">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h3 class="page-title">Subjects</h3>
                                    </div>
                                    <div class="col-auto text-end float-end ms-auto download-grp">
                                        <button type="button" id="bulkDeleteBtn" class="btn btn-danger me-2" style="display: none;">
                                            <i class="fas fa-trash"></i> Delete Selected (<span id="selectedCount">0</span>)
                                        </button>
                                        <a href="#" class="btn btn-outline-primary me-2">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                        <a href="{{ route('subject/add/page') }}" class="btn btn-primary">
                                            <i class="fas fa-plus"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table
                                    class="table border-0 star-student table-hover table-center mb-0 table-striped">
                                    <thead class="student-thread">
                                        <tr>
                                            <th>
                                                <div class="form-check check-tables">
                                                    <input class="form-check-input" type="checkbox" id="selectAll" value="">
                                                </div>
                                            </th>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Teacher</th>
                                            <th>Class</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($subjectList as $key => $value)
                                        <tr>
                                            <td>
                                                <div class="form-check check-tables">
                                                    <input class="form-check-input subject-checkbox" type="checkbox"
                                                        value="{{ $value->subject_id }}" data-subject-id="{{ $value->subject_id }}">
                                                </div>
                                            </td>
                                            <td class="subject_id">{{ $value->subject_id }}</td>
                                            <td>
                                                <h2>
                                                    <a>{{ $value->subject_name }}</a>
                                                </h2>
                                            </td>
                                            <td>
                                                @php
                                                    // Get teachers directly from teaching assignments for this subject
                                                    $teachers = $value->teachingAssignments
                                                        ->map(function($assignment) {
                                                            return optional($assignment->teacher)->full_name;
                                                        })
                                                        ->filter()
                                                        ->unique()
                                                        ->values();
                                                    
                                                    // Fallback to old teacher_name field if no assignments
                                                    if ($teachers->isEmpty() && $value->teacher_name) {
                                                        $teachers = collect([$value->teacher_name]);
                                                    }
                                                    
                                                    $displayText = $teachers->isNotEmpty() 
                                                        ? $teachers->implode(', ') 
                                                        : 'No teacher assigned';
                                                @endphp
                                                {{ $displayText }}
                                            </td>
                                            <td>
                                                @php
                                                    // Get classes from teaching assignments (pivot table) for this subject
                                                    $classes = $value->teachingAssignments
                                                        ->map(function($assignment) {
                                                            return optional($assignment->class)->class_name;
                                                        })
                                                        ->filter()
                                                        ->unique()
                                                        ->values();
                                                    
                                                    // Fallback to old class field if no assignments
                                                    if ($classes->isEmpty() && $value->class) {
                                                        $classes = collect([$value->class]);
                                                    }
                                                    
                                                    $classDisplay = $classes->isNotEmpty() 
                                                        ? $classes->implode(', ') 
                                                        : 'N/A';
                                                @endphp
                                                {{ $classDisplay }}
                                            </td>
                                            <td class="text-end">
                                                <div class="actions">
                                                    <a href="{{ url('subject/edit/'.$value->subject_id) }}" class="btn btn-sm bg-danger-light">
                                                        <i class="far fa-edit me-2"></i>
                                                    </a>
                                                    <a class="btn btn-sm bg-danger-light delete" data-bs-toggle="modal" data-bs-target="#delete">
                                                        <i class="fe fe-trash-2"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-end mt-3">
                                    {{ $subjectList->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- model elete --}}
    <div class="modal custom-modal fade" id="delete" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-header">
                        <h3>Delete Subject</h3>
                        <p>Are you sure want to delete?</p>
                    </div>
                    <div class="modal-btn delete-action">
                        <div class="row">
                            <form action="{{ route('subject/delete') }}" method="POST">
                                @csrf
                                <input type="hidden" name="subject_id" class="e_subject_id" value="">
                                <div class="row">
                                    <div class="col-6">
                                        <button type="submit" class="btn btn-primary paid-continue-btn" style="width: 100%;">Delete</button>
                                    </div>
                                    <div class="col-6">
                                        <a data-bs-dismiss="modal"
                                            class="btn btn-primary paid-cancel-btn">Cancel
                                        </a>
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
        {{-- delete js --}}
        <script>
            $(document).on('click','.delete',function()
            {
                var _this = $(this).parents('tr');
                $('.e_subject_id').val(_this.find('.subject_id').text());
            });

            // Bulk delete functionality
            $(document).ready(function() {
                // Select All checkbox
                $('#selectAll').on('change', function() {
                    $('.subject-checkbox').prop('checked', $(this).prop('checked'));
                    updateDeleteButton();
                });

                // Individual checkbox change
                $(document).on('change', '.subject-checkbox', function() {
                    // Update select all checkbox state
                    var totalCheckboxes = $('.subject-checkbox').length;
                    var checkedCheckboxes = $('.subject-checkbox:checked').length;
                    $('#selectAll').prop('checked', totalCheckboxes === checkedCheckboxes);
                    updateDeleteButton();
                });

                // Update delete button visibility and count
                function updateDeleteButton() {
                    var selectedCount = $('.subject-checkbox:checked').length;
                    if (selectedCount > 0) {
                        $('#bulkDeleteBtn').show();
                        $('#selectedCount').text(selectedCount);
                    } else {
                        $('#bulkDeleteBtn').hide();
                    }
                }

                // Bulk delete
                $('#bulkDeleteBtn').on('click', function() {
                    var selectedIds = [];
                    $('.subject-checkbox:checked').each(function() {
                        selectedIds.push($(this).val());
                    });

                    if (selectedIds.length === 0) {
                        toastr.warning('Please select at least one subject to delete');
                        return;
                    }

                    if (!confirm('Are you sure you want to delete ' + selectedIds.length + ' subject(s)? This action cannot be undone.')) {
                        return;
                    }

                    // Disable button and show loading
                    var $btn = $(this);
                    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Deleting...');

                    $.ajax({
                        url: '{{ route("subjects.bulk-delete") }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            subject_ids: selectedIds
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                // Remove deleted rows
                                $('.subject-checkbox:checked').each(function() {
                                    $(this).closest('tr').fadeOut(300, function() {
                                        $(this).remove();
                                        updateDeleteButton();
                                        // Reload if no rows left
                                        if ($('.subject-checkbox').length === 0) {
                                            location.reload();
                                        }
                                    });
                                });
                                $('#selectAll').prop('checked', false);
                            } else {
                                toastr.error(response.message || 'Failed to delete subjects');
                            }
                        },
                        error: function(xhr) {
                            var message = 'Failed to delete subjects';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }
                            toastr.error(message);
                        },
                        complete: function() {
                            $btn.prop('disabled', false).html('<i class="fas fa-trash"></i> Delete Selected (<span id="selectedCount">' + $('.subject-checkbox:checked').length + '</span>)');
                        }
                    });
                });
            });
        </script>
    @endsection

@endsection
