@extends('layouts.master')
@section('content')
{{-- message --}}
{!! Toastr::message() !!}
<div class="page-wrapper">
    <div class="content container-fluid">

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Departments</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                        <li class="breadcrumb-item active">Departments</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="student-group-form">
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="form-group">
                        <input type="text" class="form-control" id="department_id" placeholder="Search by ID ...">
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="form-group">
                        <input type="text" class="form-control" id="department_name" placeholder="Search by Name ...">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <input type="text" class="form-control"  placeholder="Search by Year ...">
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="search-student-btn">
                        <button type="btn" class="btn btn-primary">Search</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card card-table">
                    <div class="card-body">

                        <div class="page-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h3 class="page-title">Departments</h3>
                                </div>
                                <div class="col-auto text-end float-end ms-auto download-grp">
                                    <button type="button" id="bulkDeleteBtn" class="btn btn-danger me-2" style="display: none;">
                                        <i class="fas fa-trash"></i> Delete Selected (<span id="selectedCount">0</span>)
                                    </button>
                                    <a href="#" class="btn btn-outline-primary me-2">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                    <a href="{{ route('department/add/page') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <table class="table table-stripped table table-hover table-center mb-0" id="dataList">
                            <thead class="student-thread">
                                <tr>
                                    <th>
                                        <div class="form-check check-tables">
                                            <input class="form-check-input" type="checkbox" id="selectAll" value="">
                                        </div>
                                    </th>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>HOD</th>
                                    <th>Started Year</th>
                                    <th>No of Students</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                        </table>
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
                    <h3>Delete Department</h3>
                    <p>Are you sure want to delete?</p>
                </div>
                <div class="modal-btn delete-action">
                    <div class="row">
                        <form action="{{ route('department/delete') }}" method="POST">
                            @csrf
                            <input type="hidden" name="department_id" class="e_department_id" value="">
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
    {{-- get data all js --}}
    <script type="text/javascript">
        $(document).ready(function() {
        $('#dataList').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                searching: true,
                lengthMenu: [[10], [10]],
                pageLength: 10,
                ajax: {
                    url:"{{ route('get-data-list') }}",
                },
                columns: [
                    {
                        data: 'checkbox',
                        name: 'checkbox',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'department_id',
                        name: 'department_id',
                    },
                    {
                        data: 'department_name',
                        name: 'department_name',
                    },
                    {
                        data: 'head_of_department',
                        name: 'head_of_department',
                    },
                    {
                        data: 'department_start_date',
                        name: 'department_start_date',
                    },
                    {
                        data: 'no_of_students',
                        name: 'no_of_students',
                    },
                    {
                        data: 'modify',
                        name: 'modify',
                        orderable: false,
                        searchable: false,
                    },
                ]
            });
        });
    </script>

    <style>
        tr:has(.department-checkbox:checked) {
            background-color: #e3f2fd !important;
        }
        #bulkDeleteBtn {
            transition: all 0.3s ease;
        }
    </style>

    {{-- delete js --}}
<script>
    $(document).on('click','.delete',function()
    {
        var _this = $(this).parents('tr');
        $('.e_department_id').val(_this.find('.department_id').data('department_id'));
    });

    // Bulk delete functionality for DataTables
    $(document).ready(function() {
        // Select All checkbox
        $('#selectAll').on('change', function() {
            $('.department-checkbox').prop('checked', $(this).prop('checked'));
            updateDeleteButton();
        });

        // Individual checkbox change (using event delegation for dynamically loaded rows)
        $(document).on('change', '.department-checkbox', function() {
            var totalCheckboxes = $('.department-checkbox').length;
            var checkedCheckboxes = $('.department-checkbox:checked').length;
            $('#selectAll').prop('checked', totalCheckboxes > 0 && totalCheckboxes === checkedCheckboxes);
            updateDeleteButton();
        });

        function updateDeleteButton() {
            var selectedCount = $('.department-checkbox:checked').length;
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
            $('.department-checkbox:checked').each(function() {
                selectedIds.push($(this).val());
            });

            if (selectedIds.length === 0) {
                toastr.warning('Please select at least one department to delete');
                return;
            }

            if (!confirm('Are you sure you want to delete ' + selectedIds.length + ' department(s)? This action cannot be undone.')) {
                return;
            }

            var $btn = $(this);
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Deleting...');

            $.ajax({
                url: '{{ route("departments.bulk-delete") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    department_ids: selectedIds
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        // Reload DataTable
                        $('#dataList').DataTable().ajax.reload();
                        $('#selectAll').prop('checked', false);
                        updateDeleteButton();
                    } else {
                        toastr.error(response.message || 'Failed to delete departments');
                    }
                },
                error: function(xhr) {
                    var message = 'Failed to delete departments';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    toastr.error(message);
                },
                complete: function() {
                    $btn.prop('disabled', false).html('<i class="fas fa-trash"></i> Delete Selected (<span id="selectedCount">0</span>)');
                }
            });
        });
    });
</script>
@endsection
@endsection
