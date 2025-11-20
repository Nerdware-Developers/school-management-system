@extends('layouts.master')
@section('content')
{!! Toastr::message() !!}
<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Mark Attendance</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Attendance</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Filter Form -->
                        <form method="GET" action="{{ route('attendance.index') }}" class="mb-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Class <span class="text-danger">*</span></label>
                                    <select name="class_id" class="form-control" required onchange="this.form.submit()">
                                        <option value="">-- Select Class --</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}" {{ $selectedClassId == $class->id ? 'selected' : '' }}>
                                                {{ $class->class_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Date <span class="text-danger">*</span></label>
                                    <input type="date" name="date" class="form-control" value="{{ $selectedDate }}" required onchange="this.form.submit()">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">&nbsp;</label>
                                    <div>
                                        <a href="{{ route('attendance.reports') }}" class="btn btn-info">
                                            <i class="fas fa-chart-bar me-2"></i>View Reports
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>

                        @if($students->count() > 0)
                            <form method="POST" action="{{ route('attendance.store') }}">
                                @csrf
                                <input type="hidden" name="class_id" value="{{ $selectedClassId }}">
                                <input type="hidden" name="attendance_date" value="{{ $selectedDate }}">

                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Admission No</th>
                                                <th>Student Name</th>
                                                <th class="text-center">Present</th>
                                                <th class="text-center">Absent</th>
                                                <th class="text-center">Late</th>
                                                <th class="text-center">Excused</th>
                                                <th>Notes</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($students as $student)
                                                @php
                                                    $existing = $attendances->get($student->id);
                                                @endphp
                                                <tr>
                                                    <td>{{ $student->admission_number ?? 'N/A' }}</td>
                                                    <td><strong>{{ $student->first_name }} {{ $student->last_name }}</strong></td>
                                                    <td class="text-center">
                                                        <input type="radio" name="attendance[{{ $student->id }}][status]" value="present" 
                                                               {{ (!$existing || $existing->status == 'present') ? 'checked' : '' }} required>
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="radio" name="attendance[{{ $student->id }}][status]" value="absent"
                                                               {{ $existing && $existing->status == 'absent' ? 'checked' : '' }}>
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="radio" name="attendance[{{ $student->id }}][status]" value="late"
                                                               {{ $existing && $existing->status == 'late' ? 'checked' : '' }}>
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="radio" name="attendance[{{ $student->id }}][status]" value="excused"
                                                               {{ $existing && $existing->status == 'excused' ? 'checked' : '' }}>
                                                    </td>
                                                    <td>
                                                        <input type="hidden" name="attendance[{{ $student->id }}][student_id]" value="{{ $student->id }}">
                                                        <input type="text" name="attendance[{{ $student->id }}][notes]" 
                                                               class="form-control form-control-sm" 
                                                               value="{{ $existing->notes ?? '' }}" 
                                                               placeholder="Optional notes">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save me-2"></i>Save Attendance
                                    </button>
                                </div>
                            </form>
                        @elseif($selectedClassId)
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                No students found in the selected class.
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Please select a class to mark attendance.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

