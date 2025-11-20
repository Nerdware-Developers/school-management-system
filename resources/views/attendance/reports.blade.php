@extends('layouts.master')
@section('content')
{!! Toastr::message() !!}
<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Attendance Reports</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('attendance.index') }}">Attendance</a></li>
                        <li class="breadcrumb-item active">Reports</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Filter Form -->
                        <form method="GET" action="{{ route('attendance.reports') }}" class="mb-4">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Class</label>
                                    <select name="class_id" class="form-control" onchange="this.form.submit()">
                                        <option value="">-- All Classes --</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}" {{ $selectedClassId == $class->id ? 'selected' : '' }}>
                                                {{ $class->class_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Start Date</label>
                                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}" onchange="this.form.submit()">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">End Date</label>
                                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}" onchange="this.form.submit()">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div>
                                        <a href="{{ route('attendance.index') }}" class="btn btn-primary">
                                            <i class="fas fa-arrow-left me-2"></i>Mark Attendance
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>

                        @if($attendanceStats->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Admission No</th>
                                            <th>Student Name</th>
                                            <th class="text-center">Total Days</th>
                                            <th class="text-center">Present</th>
                                            <th class="text-center">Absent</th>
                                            <th class="text-center">Late</th>
                                            <th class="text-center">Excused</th>
                                            <th class="text-center">Attendance Rate</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($attendanceStats as $stat)
                                            @php
                                                $stats = $stat['stats'];
                                                $rate = $stat['attendance_rate'];
                                                $rateClass = $rate >= 90 ? 'text-success' : ($rate >= 75 ? 'text-warning' : 'text-danger');
                                            @endphp
                                            <tr>
                                                <td>{{ $stat['student']->admission_number ?? 'N/A' }}</td>
                                                <td><strong>{{ $stat['student']->first_name }} {{ $stat['student']->last_name }}</strong></td>
                                                <td class="text-center">{{ $stats->total_days ?? 0 }}</td>
                                                <td class="text-center text-success">{{ $stats->present ?? 0 }}</td>
                                                <td class="text-center text-danger">{{ $stats->absent ?? 0 }}</td>
                                                <td class="text-center text-warning">{{ $stats->late ?? 0 }}</td>
                                                <td class="text-center text-info">{{ $stats->excused ?? 0 }}</td>
                                                <td class="text-center">
                                                    <span class="fw-bold {{ $rateClass }}">
                                                        {{ number_format($rate, 1) }}%
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @elseif($selectedClassId)
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                No attendance records found for the selected criteria.
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Please select a class to view attendance reports.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

