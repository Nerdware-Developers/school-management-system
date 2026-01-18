
@extends('layouts.master')
@section('content')
{{-- message --}}
{!! Toastr::message() !!}
<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Teachers</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                        <li class="breadcrumb-item active">Teachers</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="student-group-form">
            <form method="GET" action="{{ route('teacher/list/page') }}">
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
                            <input type="text" class="form-control" name="phone" placeholder="Search by Phone ..." value="{{ request('phone') }}">
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="search-student-btn">
                            <button type="submit" class="btn btn-primary">Search</button>
                            @if(request()->hasAny(['id', 'name', 'phone']))
                                <a href="{{ route('teacher/list/page') }}" class="btn btn-secondary mt-2" style="display: block; width: 100%;">Clear</a>
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
                                    <h3 class="page-title">Teachers</h3>
                                </div>
                                <div class="col-auto text-end float-end ms-auto download-grp">
                                    <button type="button" id="bulkDeleteBtn" class="btn btn-danger me-2" style="display: none;">
                                        <i class="fas fa-trash"></i> Delete Selected (<span id="selectedCount">0</span>)
                                    </button>
                                    <a href="teachers.html" class="btn btn-outline-gray me-2 active">
                                        <i class="fa fa-list" aria-hidden="true"></i>
                                    <a href="{{ route('teacher/grid/page') }}" class="btn btn-outline-gray me-2">
                                        <i class="fa fa-th" aria-hidden="true"></i>
                                    <a href="#" id="downloadBtn" class="btn btn-outline-primary me-2"><i
                                            class="fas fa-download"></i> Download</a>
                                    <a href="{{ route('teacher/add/page') }}" class="btn btn-primary"><i class="fas fa-plus"></i></a>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table border-0 star-student table-hover table-center mb-0 table-striped">
                                <thead class="student-thread"> 
                                    <tr>
                                        <th>
                                            <div class="form-check check-tables">
                                                <input class="form-check-input" type="checkbox" id="selectAll" value="">
                                            </div>
                                        </th>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Class</th>
                                        <th>Gender</th>
                                        <th>Subject</th>
                                        <th>Section</th>
                                        <th>Mobile Number</th>
                                        <th>Address</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($listTeacher as $list)
                                    <tr>
                                        <td>
                                            <div class="form-check check-tables">
                                                <input class="form-check-input teacher-checkbox" type="checkbox" 
                                                    value="{{ $list->id }}" data-teacher-id="{{ $list->id }}">
                                            </div>
                                        </td>
                                        <td hidden>{{ $list->user_id }}</td>
                                        <td>{{ $list->user_id }}</td>
                                        <td>{{ $list->full_name }}</td>
                                        <td>
                                            @php
                                                $classes = $list->teachingAssignments->map(function($assignment) {
                                                    return optional($assignment->class)->class_name;
                                                })->filter()->unique()->values();
                                                
                                                // If no classes from assignments, check if teacher is a class teacher
                                                if ($classes->isEmpty() && $list->classTeacher) {
                                                    $classes = collect([$list->classTeacher->class_name]);
                                                }
                                                
                                                echo $classes->isNotEmpty() ? $classes->implode(', ') : 'N/A';
                                            @endphp
                                        </td>
                                        <td>{{ $list->gender }}</td>
                                        <td>
                                            @php
                                                $subjects = $list->teachingAssignments->map(function($assignment) {
                                                    return optional($assignment->subject)->subject_name;
                                                })->filter()->unique()->values();
                                                
                                                echo $subjects->isNotEmpty() ? $subjects->implode(', ') : 'N/A';
                                            @endphp
                                        </td>
                                        <td>
                                            @php
                                                // Section could be derived from class or left as N/A
                                                echo 'N/A';
                                            @endphp
                                        </td>
                                        <td>{{ $list->phone_number }}</td>
                                        <td>{{ $list->address }}</td>
                                        <td class="text-end">
                                            <div class="actions">
                                                <a href="{{ url('teacher/edit/'.$list->id) }}" class="btn btn-sm bg-danger-light">
                                                    <i class="far fa-edit me-2"></i>
                                                </a>
                                                <span class="teacher_id" hidden>{{ $list->id }}</span>
                                                <a href="javascript:void(0);" 
                                                class="btn btn-sm bg-danger-light teacher_delete" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#teacherDelete">
                                                <i class="far fa-trash-alt me-2"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-end mt-3">
                                {{ $listTeacher->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- model teacher delete --}}
<div class="modal custom-modal fade" id="teacherDelete" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="form-header">
                    <h3>Delete Student</h3>
                    <p>Are you sure want to delete?</p>
                </div>
                <div class="modal-btn delete-action">
                    <form action="{{ route('teacher/delete') }}" method="POST">
                        @csrf
                        <div class="row">
                            <input type="hidden" name="id" class="e_user_id" value="">
                            <div class="col-6">
                                <button type="submit" class="btn btn-primary continue-btn submit-btn" style="border-radius: 5px !important;">Delete</button>
                            </div>
                            <div class="col-6">
                                <a href="#" data-bs-dismiss="modal"class="btn btn-primary paid-cancel-btn">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@section('script')
    <style>
        tr:has(.teacher-checkbox:checked) {
            background-color: #e3f2fd !important;
        }
        #bulkDeleteBtn {
            transition: all 0.3s ease;
        }
    </style>
    {{-- delete js --}}
    <script>
        $(document).on('click','.teacher_delete',function()
        {
            var _this = $(this).parents('tr');
            $('.e_user_id').val(_this.find('.teacher_id').text());
        });

        // Bulk delete functionality
        $(document).ready(function() {
            $('#selectAll').on('change', function() {
                $('.teacher-checkbox').prop('checked', $(this).prop('checked'));
                updateDeleteButton();
            });

            $(document).on('change', '.teacher-checkbox', function() {
                var totalCheckboxes = $('.teacher-checkbox').length;
                var checkedCheckboxes = $('.teacher-checkbox:checked').length;
                $('#selectAll').prop('checked', totalCheckboxes === checkedCheckboxes);
                updateDeleteButton();
            });

            function updateDeleteButton() {
                var selectedCount = $('.teacher-checkbox:checked').length;
                if (selectedCount > 0) {
                    $('#bulkDeleteBtn').show();
                    $('#selectedCount').text(selectedCount);
                } else {
                    $('#bulkDeleteBtn').hide();
                }
            }

            $('#bulkDeleteBtn').on('click', function() {
                var selectedIds = [];
                $('.teacher-checkbox:checked').each(function() {
                    selectedIds.push($(this).val());
                });

                if (selectedIds.length === 0) {
                    toastr.warning('Please select at least one teacher to delete');
                    return;
                }

                if (!confirm('Are you sure you want to delete ' + selectedIds.length + ' teacher(s)? This action cannot be undone.')) {
                    return;
                }

                var $btn = $(this);
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Deleting...');

                $.ajax({
                    url: '{{ route("teachers.bulk-delete") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        teacher_ids: selectedIds
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('.teacher-checkbox:checked').each(function() {
                                $(this).closest('tr').fadeOut(300, function() {
                                    $(this).remove();
                                    updateDeleteButton();
                                    if ($('.teacher-checkbox').length === 0) {
                                        location.reload();
                                    }
                                });
                            });
                            $('#selectAll').prop('checked', false);
                        } else {
                            toastr.error(response.message || 'Failed to delete teachers');
                        }
                    },
                    error: function(xhr) {
                        var message = 'Failed to delete teachers';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        toastr.error(message);
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html('<i class="fas fa-trash"></i> Delete Selected (<span id="selectedCount">' + $('.teacher-checkbox:checked').length + '</span>)');
                    }
                });
            });

            // Download button functionality
            $('#downloadBtn').on('click', function(e) {
                e.preventDefault();
                var idFilter = $('input[name="id"]').val() || '';
                var nameFilter = $('input[name="name"]').val() || '';
                var phoneFilter = $('input[name="phone"]').val() || '';
                var params = {};
                if (idFilter) { params.id = idFilter; }
                if (nameFilter) { params.name = nameFilter; }
                if (phoneFilter) { params.phone = phoneFilter; }
                var url = '{{ route("teacher/export") }}';
                if (Object.keys(params).length > 0) { url += '?' + $.param(params); }
                window.location.href = url;
            });
        });
    </script>
@endsection
