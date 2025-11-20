@extends('layouts.master')
@section('content')
{!! Toastr::message() !!}
<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Exam Results</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('exams.page') }}">Exams</a></li>
                        <li class="breadcrumb-item active">View Results</li>
                    </ul>
                </div>
                <div class="col-auto">
                    <a href="{{ route('exams.page') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Exams
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <span class="badge {{ $examType == 'mid-term' ? 'bg-info' : 'bg-primary' }} me-2">
                                {{ ucfirst(str_replace('-', ' ', $examType)) }}
                            </span>
                            <strong>{{ $term }}</strong> - <strong>{{ $class->class_name }}</strong>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive" style="max-height: 700px; overflow-y: auto;">
                            <table class="table table-bordered table-striped table-hover" style="font-size: 0.9em;">
                                <thead class="table-dark sticky-top">
                                    <tr>
                                        <th rowspan="2" style="vertical-align: middle; min-width: 150px; position: sticky; left: 0; z-index: 11; background-color: #212529 !important;">
                                            Student Name
                                        </th>
                                        <th rowspan="2" style="vertical-align: middle; min-width: 80px; position: sticky; left: 150px; z-index: 11; background-color: #212529 !important;">
                                            Admission No
                                        </th>
                                        @foreach($exams as $exam)
                                            <th style="min-width: 100px; text-align: center;">
                                                {{ $exam->subject }}
                                                <br>
                                                <small>({{ $exam->total_marks ?? 100 }} marks)</small>
                                            </th>
                                        @endforeach
                                        <th style="min-width: 100px; text-align: center; background-color: #0d6efd !important;">
                                            Total Marks
                                        </th>
                                        <th style="min-width: 100px; text-align: center; background-color: #0d6efd !important;">
                                            Percentage
                                        </th>
                                        <th style="min-width: 80px; text-align: center; background-color: #0d6efd !important;">
                                            Grade
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($students as $student)
                                        <tr>
                                            <td style="position: sticky; left: 0; background-color: white; z-index: 10;">
                                                <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>
                                            </td>
                                            <td style="position: sticky; left: 150px; background-color: white; z-index: 10;">
                                                {{ $student->admission_number ?? 'N/A' }}
                                            </td>
                                            @foreach($exams as $exam)
                                                @php
                                                    $resultKey = $exam->id . '_' . $student->id;
                                                    $result = $results->get($resultKey);
                                                    $marks = $result ? $result->marks : null;
                                                    $totalMarks = $exam->total_marks ?? 100;
                                                    $percentage = $marks !== null && $totalMarks > 0 ? ($marks / $totalMarks) * 100 : null;
                                                @endphp
                                                <td style="text-align: center;">
                                                    @if($marks !== null)
                                                        <span class="fw-bold">{{ number_format($marks, 2) }}</span>
                                                        @if($percentage !== null)
                                                            <br>
                                                            <small class="text-muted">({{ number_format($percentage, 1) }}%)</small>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                            <td style="text-align: center; background-color: #e7f1ff;">
                                                <strong>{{ number_format($studentTotals[$student->id]['total_marks'] ?? 0, 2) }}</strong>
                                                <br>
                                                <small class="text-muted">/ {{ number_format($studentTotals[$student->id]['total_possible'] ?? 0, 2) }}</small>
                                            </td>
                                            <td style="text-align: center; background-color: #e7f1ff;">
                                                <strong>{{ number_format($studentTotals[$student->id]['percentage'] ?? 0, 2) }}%</strong>
                                            </td>
                                            <td style="text-align: center; background-color: #e7f1ff;">
                                                @php
                                                    $grade = $studentTotals[$student->id]['grade'] ?? 'F';
                                                    $gradeClass = 'bg-secondary';
                                                    if (in_array($grade, ['A+', 'A'])) $gradeClass = 'bg-success';
                                                    elseif (in_array($grade, ['B+', 'B'])) $gradeClass = 'bg-info';
                                                    elseif (in_array($grade, ['C+', 'C'])) $gradeClass = 'bg-warning';
                                                    elseif ($grade == 'D') $gradeClass = 'bg-danger';
                                                    else $gradeClass = 'bg-dark';
                                                @endphp
                                                <span class="badge {{ $gradeClass }}">{{ $grade }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ count($exams) + 5 }}" class="text-center">No students found in this class.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 d-flex justify-content-between align-items-center">
                            <div>
                                <a href="{{ route('exam.enter-marks', ['exam_type' => $examType, 'term' => $term, 'class_id' => $class->id]) }}" 
                                   class="btn btn-primary">
                                    <i class="far fa-edit me-2"></i>Edit Marks
                                </a>
                            </div>
                            <div>
                                <button onclick="window.print()" class="btn btn-secondary">
                                    <i class="fas fa-print me-2"></i>Print Results
                                </button>
                            </div>
                        </div>
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
    .table tbody td {
        vertical-align: middle;
    }
    @media print {
        .page-header, .breadcrumb, .btn, .card-header .col-auto {
            display: none !important;
        }
        .table-responsive {
            max-height: none !important;
            overflow: visible !important;
        }
    }
</style>
@endsection

