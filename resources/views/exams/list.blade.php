@extends('layouts.master')
@section('content')
{{-- message --}}
{!! Toastr::message() !!}
<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Exams</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Exams</li>
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
                                    <h3 class="page-title">Exam List</h3>
                                </div>
                                <div class="col-auto text-end float-end ms-auto download-grp">
                                    <a href="{{ route('add/exam/page') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Add Exam
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
                                        <th>Exam Type</th>
                                        <th>Term</th>
                                        <th>Class</th>
                                        <th>Subjects</th>
                                        <th>Subject Count</th>
                                        <th>Exam Date</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($examGroups as $group)
                                    <tr>
                                        <td>
                                            <div class="form-check check-tables">
                                                <input class="form-check-input" type="checkbox" value="something">
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge {{ $group->exam_type == 'mid-term' ? 'bg-info' : 'bg-primary' }}">
                                                {{ ucfirst(str_replace('-', ' ', $group->exam_type)) }}
                                            </span>
                                        </td>
                                        <td><strong>{{ $group->term }}</strong></td>
                                        <td><strong>{{ $group->class_name ?? 'N/A' }}</strong></td>
                                        <td>
                                            <div class="subjects-list" style="max-width: 300px;">
                                                <span class="text-muted small">{{ $group->subjects }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $group->subject_count }} {{ $group->subject_count == 1 ? 'Subject' : 'Subjects' }}</span>
                                        </td>
                                        <td>
                                            @if($group->exam_date)
                                                {{ \Carbon\Carbon::parse($group->exam_date)->format('M d, Y') }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="actions">
                                                <a href="{{ route('exam.view-results', ['exam_type' => $group->exam_type, 'term' => $group->term, 'class_id' => $group->class_id]) }}" 
                                                   class="btn btn-sm bg-info-light me-2"
                                                   title="View Results">
                                                    <i class="far fa-eye"></i>
                                                </a>
                                                <a href="{{ route('exam.enter-marks', ['exam_type' => $group->exam_type, 'term' => $group->term, 'class_id' => $group->class_id]) }}" 
                                                   class="btn btn-sm bg-success-light me-2"
                                                   title="Enter Marks">
                                                    <i class="far fa-edit"></i>
                                                </a>
                                                <a href="javascript:void(0);" 
                                                   class="btn btn-sm bg-danger-light exam_group_delete" 
                                                   data-bs-toggle="modal" 
                                                   data-bs-target="#examGroupDelete"
                                                   data-exam-type="{{ $group->exam_type }}"
                                                   data-term="{{ $group->term }}"
                                                   data-class-id="{{ $group->class_id }}"
                                                   title="Delete All">
                                                    <i class="far fa-trash-alt"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No exams found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-end mt-3">
                                {{ $examGroups->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Delete Group Modal --}}
<div class="modal custom-modal fade" id="examGroupDelete" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="form-header">
                    <h3>Delete All Exams</h3>
                    <p>Are you sure you want to delete all exams for this exam type, term, and class? This will delete all subjects and their results.</p>
                </div>
                <div class="modal-btn delete-action">
                    <form id="deleteGroupForm" action="{{ route('exam.delete-group') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="exam_type" id="delete_exam_type">
                        <input type="hidden" name="term" id="delete_term">
                        <input type="hidden" name="class_id" id="delete_class_id">
                        <div class="row">
                            <div class="col-6">
                                <button type="submit" class="btn btn-primary continue-btn submit-btn" style="border-radius: 5px !important;">Delete All</button>
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
    $(document).on('click', '.exam_group_delete', function() {
        var examType = $(this).data('exam-type');
        var term = $(this).data('term');
        var classId = $(this).data('class-id');
        $('#delete_exam_type').val(examType);
        $('#delete_term').val(term);
        $('#delete_class_id').val(classId);
    });
</script>
@endsection
@endsection

