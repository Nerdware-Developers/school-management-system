@extends('layouts.master')
@section('content')
{{-- message --}}
{!! Toastr::message() !!}
<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Employers</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Employers</li>
                    </ul>
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
                                    <h3 class="page-title">Employers</h3>
                                </div>
                                <div class="col-auto text-end float-end ms-auto download-grp">
                                    <a href="{{ route('employers.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Add Employer
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table border-0 star-student table-hover table-center mb-0 table-striped">
                                <thead class="student-thread">
                                    <tr>
                                        <th>
                                            <div class="form-check check-tables">
                                                <input class="form-check-input" type="checkbox" value="something">
                                            </div>
                                        </th>
                                        <th>Employee ID</th>
                                        <th>Name</th>
                                        <th>Position</th>
                                        <th>Department</th>
                                        <th>Gender</th>
                                        <th>Phone Number</th>
                                        <th>Email</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($employees as $employee)
                                    <tr>
                                        <td>
                                            <div class="form-check check-tables">
                                                <input class="form-check-input" type="checkbox" value="something">
                                            </div>
                                        </td>
                                        <td>{{ $employee->employee_id ?? $employee->user_id ?? 'N/A' }}</td>
                                        <td>
                                            <h2 class="table-avatar">
                                                @if(isset($employee->type) && $employee->type === 'teacher')
                                                    <a href="{{ url('teacher/profile/' . $employee->id) }}">
                                                        {{ $employee->full_name }}
                                                    </a>
                                                @else
                                                    <a href="{{ route('employers.show', $employee->id) }}">
                                                        {{ $employee->full_name }}
                                                    </a>
                                                @endif
                                            </h2>
                                        </td>
                                        <td>{{ $employee->position ?? 'N/A' }}</td>
                                        <td>{{ $employee->department ?? 'N/A' }}</td>
                                        <td>{{ $employee->gender ?? 'N/A' }}</td>
                                        <td>{{ $employee->phone_number ?? 'N/A' }}</td>
                                        <td>{{ $employee->email ?? 'N/A' }}</td>
                                        <td class="text-end">
                                            <div class="actions">
                                                @if(isset($employee->type) && $employee->type === 'teacher')
                                                    <a href="{{ url('teacher/profile/' . $employee->id) }}" class="btn btn-sm bg-success-light">
                                                        <i class="far fa-eye me-2"></i>View
                                                    </a>
                                                    <a href="{{ url('teacher/edit/' . $employee->id) }}" class="btn btn-sm bg-danger-light">
                                                        <i class="far fa-edit me-2"></i>
                                                    </a>
                                                @else
                                                    <a href="{{ route('employers.show', $employee->id) }}" class="btn btn-sm bg-success-light">
                                                        <i class="far fa-eye me-2"></i>View
                                                    </a>
                                                    <a href="{{ route('employers.edit', $employee->id) }}" class="btn btn-sm bg-danger-light">
                                                        <i class="far fa-edit me-2"></i>
                                                    </a>
                                                    <a href="javascript:void(0);" 
                                                       class="btn btn-sm bg-danger-light employer_delete" 
                                                       data-bs-toggle="modal" 
                                                       data-bs-target="#employerDelete"
                                                       data-id="{{ $employee->id }}">
                                                        <i class="far fa-trash-alt me-2"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No employees found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-end mt-3">
                                {{ $employees->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Delete Modal --}}
<div class="modal custom-modal fade" id="employerDelete" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="form-header">
                    <h3>Delete Employer</h3>
                    <p>Are you sure want to delete?</p>
                </div>
                <div class="modal-btn delete-action">
                    <form id="deleteForm" action="" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="row">
                            <div class="col-6">
                                <button type="submit" class="btn btn-primary continue-btn submit-btn" style="border-radius: 5px !important;">Delete</button>
                            </div>
                            <div class="col-6">
                                <a href="#" data-bs-dismiss="modal" class="btn btn-primary paid-cancel-btn">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@section('script')
<script>
    $(document).on('click', '.employer_delete', function() {
        var id = $(this).data('id');
        $('#deleteForm').attr('action', '{{ url("employers") }}/' + id);
    });
</script>
@endsection
@endsection

