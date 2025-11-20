@extends('layouts.master')
@section('content')
{!! Toastr::message() !!}
<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Enter Exam Marks</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('exams.page') }}">Exams</a></li>
                        <li class="breadcrumb-item active">Enter Marks</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Filter Form -->
                        <form method="GET" action="{{ route('exam.enter-marks') }}" class="mb-4">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Exam Type <span class="text-danger">*</span></label>
                                    <select name="exam_type" class="form-control" required>
                                        <option value="">-- Select Type --</option>
                                        @foreach($examTypes as $key => $label)
                                            <option value="{{ $key }}" {{ (isset($examType) && $examType == $key) ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Term <span class="text-danger">*</span></label>
                                    <select name="term" class="form-control" required>
                                        <option value="">-- Select Term --</option>
                                        @foreach($terms as $t)
                                            <option value="{{ $t }}" {{ (isset($term) && $term == $t) ? 'selected' : '' }}>
                                                {{ $t }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Class <span class="text-danger">*</span></label>
                                    <select name="class_id" class="form-control" required>
                                        <option value="">-- Select Class --</option>
                                        @foreach($classes as $c)
                                            <option value="{{ $c->id }}" {{ (isset($classId) && $classId == $c->id) ? 'selected' : '' }}>
                                                {{ $c->class_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary w-100">Load</button>
                                </div>
                            </div>
                        </form>

                        @if(isset($exams) && $exams->count() > 0)
                            <div class="alert alert-success">
                                <strong>{{ isset($examType) ? ucfirst(str_replace('-', ' ', $examType)) : '' }} - {{ $term ?? '' }}</strong> for <strong>{{ $class->class_name ?? '' }}</strong>
                                <br>
                                <small>Enter marks for all subjects. Leave blank if student did not take the exam.</small>
                            </div>

                            <form method="POST" action="{{ route('exam.save-marks') }}">
                                @csrf
                                <input type="hidden" name="exam_type" value="{{ $examType }}">
                                <input type="hidden" name="term" value="{{ $term }}">
                                <input type="hidden" name="class_id" value="{{ $classId }}">

                                <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                                    <table class="table table-bordered table-striped" style="font-size: 0.9em;">
                                        <thead class="table-dark sticky-top">
                                            <tr>
                                                <th rowspan="2" style="vertical-align: middle; min-width: 150px;">Student Name</th>
                                                <th rowspan="2" style="vertical-align: middle; min-width: 80px;">Admission No</th>
                                                @foreach($exams as $exam)
                                                    <th style="min-width: 100px;">
                                                        {{ $exam->subject }}
                                                        <br>
                                                        <small>({{ $exam->total_marks ?? 100 }} marks)</small>
                                                    </th>
                                                @endforeach
                                                <th rowspan="2" style="vertical-align: middle; min-width: 120px; background-color: #0d6efd !important;">
                                                    Total
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($students as $student)
                                                <tr data-student-id="{{ $student->id }}">
                                                    <td><strong>{{ $student->first_name }} {{ $student->last_name }}</strong></td>
                                                    <td>{{ $student->admission_number ?? 'N/A' }}</td>
                                                    @php
                                                        $initialTotal = 0;
                                                    @endphp
                                                    @foreach($exams as $exam)
                                                        @php
                                                            $resultKey = $exam->id . '_' . $student->id;
                                                            $existingMarks = $results->get($resultKey)->marks ?? null;
                                                            if ($existingMarks !== null) {
                                                                $initialTotal += $existingMarks;
                                                            }
                                                        @endphp
                                                        <td>
                                                            <input type="number" 
                                                                   name="marks[{{ $exam->id }}][{{ $student->id }}]" 
                                                                   class="form-control form-control-sm marks-input" 
                                                                   data-exam-id="{{ $exam->id }}"
                                                                   data-student-id="{{ $student->id }}"
                                                                   data-max-marks="{{ $exam->total_marks ?? 100 }}"
                                                                   value="{{ $existingMarks }}"
                                                                   min="0"
                                                                   step="0.01"
                                                                   max="{{ $exam->total_marks ?? 100 }}"
                                                                   placeholder="0.00">
                                                        </td>
                                                    @endforeach
                                                    <td class="text-center" style="background-color: #e7f1ff;">
                                                        <strong class="total-marks" data-student-id="{{ $student->id }}">
                                                            {{ number_format($initialTotal, 2) }}
                                                        </strong>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save me-2"></i>Save All Marks
                                    </button>
                                    <a href="{{ route('exams.page') }}" class="btn btn-secondary">Cancel</a>
                                </div>
                            </form>
                        @elseif(isset($examType) && isset($term) && isset($classId))
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                No exams found for the selected criteria. 
                                <a href="{{ route('add/exam/page') }}" class="alert-link">Create exams first</a>.
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Please select exam type, term, and class to enter marks.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .table thead th {
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #212529 !important;
        color: white;
    }
    .table tbody td input {
        text-align: center;
    }
    .total-marks {
        font-size: 1.1em;
        color: #0d6efd;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get all mark input fields
    const markInputs = document.querySelectorAll('.marks-input');
    
    // Function to calculate total for a student
    function calculateTotal(studentId) {
        const studentRow = document.querySelector(`tr[data-student-id="${studentId}"]`);
        if (!studentRow) return;
        
        const inputs = studentRow.querySelectorAll('.marks-input');
        let total = 0;
        
        inputs.forEach(input => {
            const value = parseFloat(input.value) || 0;
            total += value;
        });
        
        const totalElement = studentRow.querySelector(`.total-marks[data-student-id="${studentId}"]`);
        if (totalElement) {
            totalElement.textContent = total.toFixed(2);
        }
    }
    
    // Add event listeners to all mark inputs
    markInputs.forEach(input => {
        input.addEventListener('input', function() {
            const studentId = this.getAttribute('data-student-id');
            calculateTotal(studentId);
        });
        
        input.addEventListener('blur', function() {
            const maxMarks = parseFloat(this.getAttribute('data-max-marks')) || 100;
            const value = parseFloat(this.value) || 0;
            
            if (value > maxMarks) {
                this.value = maxMarks;
                const studentId = this.getAttribute('data-student-id');
                calculateTotal(studentId);
            }
        });
    });
    
    // Calculate initial totals for all students
    const studentIds = new Set();
    markInputs.forEach(input => {
        studentIds.add(input.getAttribute('data-student-id'));
    });
    
    studentIds.forEach(studentId => {
        calculateTotal(studentId);
    });
});
</script>
@endsection

