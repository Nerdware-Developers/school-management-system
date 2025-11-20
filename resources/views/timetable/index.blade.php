@extends('layouts.master')
@section('content')
{!! Toastr::message() !!}
<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Timetable</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Timetable</li>
                    </ul>
                </div>
                <div class="col-auto">
                    <form method="GET" class="d-inline">
                        <select name="class_id" class="form-select d-inline-block" style="width: auto;" onchange="this.form.submit()">
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ $selectedClassId == $class->id ? 'selected' : '' }}>
                                    {{ $class->class_name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                    @if($selectedClassId)
                        <a href="{{ route('timetable.create', ['class_id' => $selectedClassId]) }}" class="btn btn-primary ms-2">
                            <i class="fas fa-plus me-2"></i>Create/Edit Timetable
                        </a>
                        <a href="javascript:void(0);" class="btn btn-danger ms-2" onclick="deleteTimetable({{ $selectedClassId }})">
                            <i class="fas fa-trash me-2"></i>Delete
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        @if($selectedClassId && $timetable->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Day/Period</th>
                                            @for($i = 1; $i <= 8; $i++)
                                                <th class="text-center">Period {{ $i }}</th>
                                            @endfor
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                        @endphp
                                        @foreach($days as $day)
                                            <tr>
                                                <td class="fw-bold">{{ $day }}</td>
                                                @for($period = 1; $period <= 8; $period++)
                                                    @php
                                                        $entry = $timetable->get($day)?->firstWhere('period_number', $period);
                                                    @endphp
                                                    <td class="text-center" style="min-width: 150px;">
                                                        @if($entry)
                                                            <div class="p-2 border rounded" style="background-color: #e7f1ff;">
                                                                <strong>{{ $entry->subject->subject_name ?? 'N/A' }}</strong><br>
                                                                <small class="text-muted">
                                                                    {{ $entry->teacher->full_name ?? 'TBA' }}<br>
                                                                    {{ date('H:i', strtotime($entry->start_time)) }} - {{ date('H:i', strtotime($entry->end_time)) }}
                                                                    @if($entry->room)
                                                                        <br>{{ $entry->room }}
                                                                    @endif
                                                                </small>
                                                            </div>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                @endfor
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @elseif($selectedClassId)
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                No timetable found for this class. 
                                <a href="{{ route('timetable.create', ['class_id' => $selectedClassId]) }}" class="alert-link">Create one now</a>.
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Please select a class to view timetable.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
function deleteTimetable(classId) {
    if (confirm('Are you sure you want to delete the timetable for this class?')) {
        const form = document.getElementById('deleteForm');
        form.action = '{{ url("timetable") }}/' + classId;
        form.submit();
    }
}
</script>
@endsection

