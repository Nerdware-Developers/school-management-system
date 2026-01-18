
@extends('layouts.master')
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-sub-header">
                            <h3 class="page-title">Students</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('student/list') }}">Student</a></li>
                                <li class="breadcrumb-item active">All Students</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            {{-- message --}}
            {!! Toastr::message() !!}
            <div class="student-group-form">
                <form id="searchForm" method="GET" action="{{ route('student/list') }}">
                    <div class="row">
                        <div class="col-lg-3 col-md-6">
                            <div class="form-group">
                                <input type="text" name="class" class="form-control"
                                    placeholder="Search by class..." value="{{ request('class') }}">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="form-group">
                                <input type="text" name="name" class="form-control"
                                    placeholder="Search by name..." value="{{ request('name') }}">
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="search-student-btn">
                                <button type="submit" class="btn btn-primary">Search</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            @if(request('class'))
                <h5>Showing students in class: <strong>{{ request('class') }}</strong></h5>
            @endif
            <div class="row">
                <div class="col-sm-12">
                    <div class="card card-table comman-shadow">
                        <div class="card-body">
                            <div class="page-header">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h3 class="page-title">Students</h3>
                                    </div>
                                    <div class="col-auto text-end float-end ms-auto download-grp">
                                        <button type="button" id="bulkDeleteBtn" class="btn btn-danger me-2" style="display: none;">
                                            <i class="fas fa-trash"></i> Delete Selected (<span id="selectedCount">0</span>)
                                        </button>
                                        <a href="{{ route('student/list') }}" class="btn btn-outline-gray me-2 active">
                                            <i class="fa fa-list" aria-hidden="true"></i>
                                        </a>
                                        <a href="{{ route('student/list-by-class') }}" class="btn btn-outline-gray me-2" title="View by Class">
                                            <i class="fas fa-layer-group" aria-hidden="true"></i>
                                        </a>
                                        <a href="{{ route('student/grid') }}" class="btn btn-outline-gray me-2">
                                            <i class="fa fa-th" aria-hidden="true"></i>
                                        </a>
                                        <a href="#" id="downloadBtn" class="btn btn-outline-primary me-2"><i class="fas fa-download"></i> Download</a>
                                        <a href="{{ route('student/add/page') }}" class="btn btn-primary"><i class="fas fa-plus"></i></a>
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
                                            <th>ADM</th>
                                            <th>Name</th>
                                            <th>Class</th>
                                            <th>DOB</th>
                                            <th>Parent Name</th>
                                            <th>Mobile Number</th>
                                            <th>Address</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($studentList as $key=>$list )
                                        <tr>
                                            <td>
                                                <div class="form-check check-tables">
                                                    <input class="form-check-input student-checkbox" type="checkbox" 
                                                        value="{{ $list->id }}" data-student-id="{{ $list->id }}" data-avatar="{{ $list->image }}">
                                                </div>
                                            </td>
                                            <td>{{ $list->admission_number }}</td>
                                            <td hidden class="id">{{ $list->id }}</td>
                                            <td hidden class="avatar">{{ $list->image }}</td>
                                            <td>
                                                <h2 class="table-avatar">
                                                    <a href="{{ url('student/profile/' . $list->id) }}" class="avatar avatar-sm me-2">
                                                        <img src="{{ $list->image ? route('student.photo', $list->image) : asset('images/photo_defaults.jpg') }}"
                                                            alt="Student Image"
                                                            class="avatar-img rounded-circle">
                                                    </a>
                                                    <a href="{{ url('student/profile/' . $list->id) }}">
                                                        {{ $list->first_name }} {{ $list->last_name }}
                                                    </a>
                                                </h2>
                                            </td>
                                            <td>{{ $list->class }} {{ $list->section }}</td>
                                            <td>{{ $list->date_of_birth }}</td>
                                            <td>{{ $list->parent_name }}</td>
                                            <td>{{ $list->parent_number }}</td>
                                            <td>{{$list->address}}</td>
                                            <td class="text-end">
                                                <div class="actions">
                                                    <a href="{{ url('student/edit/'.$list->id) }}" class="btn btn-sm bg-danger-light">
                                                        <i class="far fa-edit me-2"></i>
                                                    </a>
                                                    <a class="btn btn-sm bg-danger-light student_delete" data-bs-toggle="modal" data-bs-target="#studentUser">
                                                        <i class="far fa-trash-alt me-2"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-end mt-3">
                                    {{ $studentList->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- model student delete --}}
    <div class="modal custom-modal fade" id="studentUser" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-header">
                        <h3>Delete Student</h3>
                        <p>Are you sure want to delete?</p>
                    </div>
                    <div class="modal-btn delete-action">
                        <form action="{{ route('student/delete') }}" method="POST">
                            @csrf
                            <div class="row">
                                <input type="hidden" name="id" class="e_id" value="">
                                <input type="hidden" name="avatar" class="e_avatar" value="">
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
        tr:has(.student-checkbox:checked) {
            background-color: #e3f2fd !important;
        }
        #bulkDeleteBtn {
            transition: all 0.3s ease;
        }
    </style>

    {{-- delete js --}}
    <script>
        $(document).on('click','.student_delete',function()
        {
            var _this = $(this).parents('tr');
            $('.e_id').val(_this.find('.id').text());
            $('.e_avatar').val(_this.find('.avatar').text());
        });

        // Bulk delete functionality
        $(document).ready(function() {
            // Select All checkbox
            $('#selectAll').on('change', function() {
                $('.student-checkbox').prop('checked', $(this).prop('checked'));
                updateDeleteButton();
            });

            // Individual checkbox change
            $(document).on('change', '.student-checkbox', function() {
                var totalCheckboxes = $('.student-checkbox').length;
                var checkedCheckboxes = $('.student-checkbox:checked').length;
                $('#selectAll').prop('checked', totalCheckboxes === checkedCheckboxes);
                updateDeleteButton();
            });

            // Update delete button visibility and count
            function updateDeleteButton() {
                var selectedCount = $('.student-checkbox:checked').length;
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
                $('.student-checkbox:checked').each(function() {
                    selectedIds.push($(this).val());
                });

                if (selectedIds.length === 0) {
                    toastr.warning('Please select at least one student to delete');
                    return;
                }

                if (!confirm('Are you sure you want to delete ' + selectedIds.length + ' student(s)? This action cannot be undone.')) {
                    return;
                }

                var $btn = $(this);
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Deleting...');

                $.ajax({
                    url: '{{ route("students.bulk-delete") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        student_ids: selectedIds
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('.student-checkbox:checked').each(function() {
                                $(this).closest('tr').fadeOut(300, function() {
                                    $(this).remove();
                                    updateDeleteButton();
                                    if ($('.student-checkbox').length === 0) {
                                        location.reload();
                                    }
                                });
                            });
                            $('#selectAll').prop('checked', false);
                        } else {
                            toastr.error(response.message || 'Failed to delete students');
                        }
                    },
                    error: function(xhr) {
                        var message = 'Failed to delete students';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        toastr.error(message);
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html('<i class="fas fa-trash"></i> Delete Selected (<span id="selectedCount">' + $('.student-checkbox:checked').length + '</span>)');
                    }
                });
            });
        });
    </script>
    <script>
$(document).ready(function () {
    // Live search for class and name
    $('input[name="class"], input[name="name"]').on('keyup', function () {
        const form = $('#searchForm');
        const formData = form.serialize();

        $.ajax({
            url: form.attr('action'),
            data: formData,
            type: 'GET',
            success: function (response) {
                const newBody = $(response).find('tbody').html();
                $('tbody').html(newBody);
            },
            error: function () {
                console.error("Failed to fetch filtered students");
            }
        });
    });

    // Download button - preserve current filters
    $('#downloadBtn').on('click', function(e) {
        e.preventDefault();
        
        // Get current filter values from form inputs
        var classFilter = $('input[name="class"]').val() || '';
        var nameFilter = $('input[name="name"]').val() || '';
        
        // Build query string
        var params = {};
        if (classFilter) {
            params.class = classFilter;
        }
        if (nameFilter) {
            params.name = nameFilter;
        }
        
        // Build URL with query parameters
        var url = '{{ route("student/export") }}';
        if (Object.keys(params).length > 0) {
            url += '?' + $.param(params);
        }
        
        // Navigate to export URL
        window.location.href = url;
    });
});
</script>
    @endsection

@endsection
