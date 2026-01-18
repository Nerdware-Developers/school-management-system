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
                                                            <select name="timetable[{{ $day }}_{{ $period['number'] }}][subject_id]" 
                                                                    class="form-control subject-select" 
                                                                    data-day="{{ $day }}" 
                                                                    data-period="{{ $period['number'] }}"
                                                                    data-class-id="{{ $selectedClassId }}">
                                                                <option value="">-- Select Subject --</option>
                                                                @foreach($subjects as $subject)
                                                                    <option value="{{ $subject->id }}">{{ $subject->subject_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select name="timetable[{{ $day }}_{{ $period['number'] }}][teacher_id]" 
                                                                    class="form-control teacher-select" 
                                                                    data-day="{{ $day }}" 
                                                                    data-period="{{ $period['number'] }}">
                                                                <option value="">-- Select Teacher --</option>
                                                                @foreach($teachers as $teacher)
                                                                    <option value="{{ $teacher->id }}">{{ $teacher->full_name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <small class="text-danger collision-warning" style="display: none;"></small>
                                                        </td>
                                                        <td>
                                                            <input type="time" name="timetable[{{ $day }}_{{ $period['number'] }}][start_time]" 
                                                                   class="form-control start-time" 
                                                                   data-day="{{ $day }}" 
                                                                   data-period="{{ $period['number'] }}"
                                                                   value="{{ $period['start'] }}" required>
                                                        </td>
                                                        <td>
                                                            <input type="time" name="timetable[{{ $day }}_{{ $period['number'] }}][end_time]" 
                                                                   class="form-control end-time" 
                                                                   data-day="{{ $day }}" 
                                                                   data-period="{{ $period['number'] }}"
                                                                   value="{{ $period['end'] }}" required>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const classId = {{ $selectedClassId }};
    const subjectSelects = document.querySelectorAll('.subject-select');
    const teacherSelects = document.querySelectorAll('.teacher-select');
    
    // Auto-fill teacher when subject is selected
    subjectSelects.forEach(select => {
        select.addEventListener('change', function() {
            const subjectId = this.value;
            const day = this.dataset.day;
            const period = this.dataset.period;
            const teacherSelect = document.querySelector(`select[name="timetable[${day}_${period}][teacher_id]"]`);
            const warningElement = teacherSelect?.parentElement.querySelector('.collision-warning');
            
            if (!subjectId || !teacherSelect) return;
            
            // Reset teacher selection
            teacherSelect.value = '';
            if (warningElement) {
                warningElement.style.display = 'none';
                warningElement.textContent = '';
            }
            
            // Fetch teacher for this subject and class
            fetch(`{{ route('timetable.get-teacher') }}?subject_id=${subjectId}&class_id=${classId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.teacher_id) {
                        teacherSelect.value = data.teacher_id;
                        
                        // Check for collisions after auto-filling
                        checkTeacherCollision(day, period);
                    }
                })
                .catch(error => {
                    console.error('Error fetching teacher:', error);
                });
        });
    });
    
    // Check for collisions when teacher, time, or day changes
    teacherSelects.forEach(select => {
        select.addEventListener('change', function() {
            const day = this.dataset.day;
            const period = this.dataset.period;
            checkTeacherCollision(day, period);
        });
    });
    
    document.querySelectorAll('.start-time, .end-time').forEach(input => {
        input.addEventListener('change', function() {
            const day = this.dataset.day;
            const period = this.dataset.period;
            checkTeacherCollision(day, period);
        });
    });
    
    function checkTeacherCollision(day, period) {
        const teacherSelect = document.querySelector(`select[name="timetable[${day}_${period}][teacher_id]"]`);
        const startTimeInput = document.querySelector(`input[name="timetable[${day}_${period}][start_time]"]`);
        const endTimeInput = document.querySelector(`input[name="timetable[${day}_${period}][end_time]"]`);
        const warningElement = teacherSelect?.parentElement.querySelector('.collision-warning');
        
        if (!teacherSelect || !startTimeInput || !endTimeInput || !warningElement) return;
        
        const teacherId = teacherSelect.value;
        const startTime = startTimeInput.value;
        const endTime = endTimeInput.value;
        
        if (!teacherId || !startTime || !endTime) {
            warningElement.style.display = 'none';
            warningElement.textContent = '';
            return;
        }
        
        // Validate end time is after start time
        if (startTime >= endTime) {
            warningElement.style.display = 'block';
            warningElement.textContent = 'End time must be after start time';
            warningElement.style.color = '#dc3545';
            return;
        }
        
        // Check for collisions
        fetch('{{ route("timetable.check-collision") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                teacher_id: teacherId,
                day: day,
                start_time: startTime,
                end_time: endTime,
                class_id: classId,
                exclude_period: period
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.has_collision) {
                warningElement.style.display = 'block';
                warningElement.textContent = data.message;
                warningElement.style.color = '#dc3545';
                teacherSelect.classList.add('is-invalid');
            } else {
                warningElement.style.display = 'none';
                warningElement.textContent = '';
                teacherSelect.classList.remove('is-invalid');
            }
        })
        .catch(error => {
            console.error('Error checking collision:', error);
        });
    }
    
    // Prevent form submission if there are collisions
    document.querySelector('form[method="POST"]').addEventListener('submit', function(e) {
        const warnings = document.querySelectorAll('.collision-warning[style*="display: block"]');
        if (warnings.length > 0) {
            e.preventDefault();
            alert('Please resolve teacher time collisions before submitting.');
            return false;
        }
    });
});
</script>
@endsection

