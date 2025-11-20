@extends('layouts.master')
@section('content')
{!! Toastr::message() !!}
<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Create Timetable</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('timetable.index') }}">Timetable</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('timetable.create') }}" class="mb-4">
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
                            </div>
                        </form>

                        @if($selectedClassId)
                            <form method="POST" action="{{ route('timetable.store') }}">
                                @csrf
                                <input type="hidden" name="class_id" value="{{ $selectedClassId }}">

                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Day</th>
                                                <th>Period</th>
                                                <th>Subject</th>
                                                <th>Teacher</th>
                                                <th>Start Time</th>
                                                <th>End Time</th>
                                                <th>Room</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($days as $day)
                                                @foreach($periods as $period)
                                                    <tr>
                                                        <td>
                                                            <input type="hidden" name="timetable[{{ $day }}_{{ $period['number'] }}][day]" value="{{ $day }}">
                                                            <input type="hidden" name="timetable[{{ $day }}_{{ $period['number'] }}][period_number]" value="{{ $period['number'] }}">
                                                            <strong>{{ $day }}</strong>
                                                        </td>
                                                        <td>
                                                            <strong>{{ $period['name'] }}</strong>
                                                        </td>
                                                        <td>
                                                            <select name="timetable[{{ $day }}_{{ $period['number'] }}][subject_id]" class="form-control">
                                                                <option value="">-- Select Subject --</option>
                                                                @foreach($subjects as $subject)
                                                                    <option value="{{ $subject->id }}">{{ $subject->subject_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select name="timetable[{{ $day }}_{{ $period['number'] }}][teacher_id]" class="form-control">
                                                                <option value="">-- Select Teacher --</option>
                                                                @foreach($teachers as $teacher)
                                                                    <option value="{{ $teacher->id }}">{{ $teacher->full_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="time" name="timetable[{{ $day }}_{{ $period['number'] }}][start_time]" 
                                                                   class="form-control" value="{{ $period['start'] }}" required>
                                                        </td>
                                                        <td>
                                                            <input type="time" name="timetable[{{ $day }}_{{ $period['number'] }}][end_time]" 
                                                                   class="form-control" value="{{ $period['end'] }}" required>
                                                        </td>
                                                        <td>
                                                            <input type="text" name="timetable[{{ $day }}_{{ $period['number'] }}][room]" 
                                                                   class="form-control" placeholder="Room number">
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save me-2"></i>Save Timetable
                                    </button>
                                    <a href="{{ route('timetable.index', ['class_id' => $selectedClassId]) }}" class="btn btn-secondary">Cancel</a>
                                </div>
                            </form>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Please select a class to create timetable.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

