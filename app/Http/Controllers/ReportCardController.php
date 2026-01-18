<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Classe;
use App\Models\Exam;
use App\Models\ExamResult;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportCardController extends Controller
{
    /**
     * Show report card generation page
     */
    public function index(Request $request)
    {
        $classes = Classe::orderBy('class_name')->get();
        $selectedClassId = $request->query('class_id');
        $selectedTerm = $request->query('term');
        $selectedExamType = $request->query('exam_type');

        $students = collect();
        if ($selectedClassId) {
            $classe = Classe::findOrFail($selectedClassId);
            $students = Student::where('class', $classe->class_name)
                ->orderBy('roll')
                ->orderBy('first_name')
                ->get();
        }

        $terms = ['Term 1', 'Term 2', 'Term 3'];
        $examTypes = ['mid-term' => 'Mid-Term', 'end-term' => 'End-Term'];

        return view('report-cards.index', compact(
            'classes',
            'selectedClassId',
            'selectedTerm',
            'selectedExamType',
            'students',
            'terms',
            'examTypes'
        ));
    }

    /**
     * Generate report card PDF
     */
    public function generate(Request $request, $studentId)
    {
        $student = Student::with(['attendances'])->findOrFail($studentId);
        
        $term = $request->query('term');
        $examType = $request->query('exam_type');

        if (!$term || !$examType) {
            Toastr::error('Please select term and exam type.', 'Error');
            return redirect()->back();
        }

        // Get class
        $classe = Classe::where('class_name', $student->class)->first();
        if (!$classe) {
            Toastr::error('Student class not found.', 'Error');
            return redirect()->back();
        }

        // Get exams for this term and exam type
        $exams = Exam::where('class_id', $classe->id)
            ->where('term', $term)
            ->where('exam_type', $examType)
            ->orderBy('subject')
            ->get();

        if ($exams->isEmpty()) {
            Toastr::error('No exam results found for the selected criteria.', 'Error');
            return redirect()->back();
        }

        // Get exam results
        $examIds = $exams->pluck('id');
        $results = ExamResult::where('student_id', $studentId)
            ->whereIn('exam_id', $examIds)
            ->with('exam')
            ->get()
            ->keyBy('exam_id');

        // Calculate totals
        $totalMarks = 0;
        $totalPossible = 0;
        $subjectResults = [];

        foreach ($exams as $exam) {
            $result = $results->get($exam->id);
            $marks = $result ? $result->marks : null;
            $totalMarksForSubject = $exam->total_marks ?? 100;
            
            if ($marks !== null) {
                $totalMarks += $marks;
            }
            $totalPossible += $totalMarksForSubject;

            $percentage = $marks !== null && $totalMarksForSubject > 0 
                ? ($marks / $totalMarksForSubject) * 100 
                : null;

            $grade = $this->calculateGrade($percentage);

            $subjectResults[] = [
                'subject' => $exam->subject,
                'marks' => $marks,
                'total_marks' => $totalMarksForSubject,
                'percentage' => $percentage,
                'grade' => $grade,
            ];
        }

        $overallPercentage = $totalPossible > 0 ? ($totalMarks / $totalPossible) * 100 : 0;
        $overallGrade = $this->calculateGrade($overallPercentage);

        // Get attendance stats
        $attendanceStats = DB::table('attendances')
            ->where('student_id', $studentId)
            ->selectRaw('
                COUNT(*) as total_days,
                SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent
            ')
            ->first();

        $data = [
            'student' => $student,
            'classe' => $classe,
            'term' => $term,
            'examType' => $examType,
            'subjectResults' => $subjectResults,
            'totalMarks' => $totalMarks,
            'totalPossible' => $totalPossible,
            'overallPercentage' => $overallPercentage,
            'overallGrade' => $overallGrade,
            'attendanceStats' => $attendanceStats,
        ];

        // Generate PDF report card
        $pdf = Pdf::loadView('report-cards.pdf', $data);
        $filename = "Report_Card_{$student->first_name}_{$student->last_name}_{$term}_{$examType}.pdf";
        $filename = str_replace(' ', '_', $filename);
        return $pdf->download($filename);
    }

    /**
     * Generate academic transcript
     */
    public function transcript($studentId)
    {
        $student = Student::findOrFail($studentId);
        
        $classe = Classe::where('class_name', $student->class)->first();
        if (!$classe) {
            Toastr::error('Student class not found.', 'Error');
            return redirect()->back();
        }

        // Get all exam results grouped by term and exam type
        $exams = Exam::where('class_id', $classe->id)
            ->whereNotNull('exam_type')
            ->orderBy('term')
            ->orderBy('exam_type')
            ->orderBy('subject')
            ->get();

        $examIds = $exams->pluck('id');
        $results = ExamResult::where('student_id', $studentId)
            ->whereIn('exam_id', $examIds)
            ->with('exam')
            ->get()
            ->keyBy('exam_id');

        // Group by term and exam type
        $transcriptData = [];
        foreach ($exams as $exam) {
            $key = $exam->term . '_' . $exam->exam_type;
            if (!isset($transcriptData[$key])) {
                $transcriptData[$key] = [
                    'term' => $exam->term,
                    'exam_type' => $exam->exam_type,
                    'subjects' => [],
                ];
            }

            $result = $results->get($exam->id);
            $marks = $result ? $result->marks : null;
            $totalMarks = $exam->total_marks ?? 100;
            $percentage = $marks !== null && $totalMarks > 0 ? ($marks / $totalMarks) * 100 : null;
            $grade = $this->calculateGrade($percentage);

            $transcriptData[$key]['subjects'][] = [
                'subject' => $exam->subject,
                'marks' => $marks,
                'total_marks' => $totalMarks,
                'percentage' => $percentage,
                'grade' => $grade,
            ];
        }

        $data = [
            'student' => $student,
            'classe' => $classe,
            'transcriptData' => $transcriptData,
        ];

        // Generate PDF transcript
        $pdf = Pdf::loadView('report-cards.transcript', $data);
        $filename = "Transcript_{$student->first_name}_{$student->last_name}.pdf";
        $filename = str_replace(' ', '_', $filename);
        return $pdf->download($filename);
    }

    /**
     * Calculate grade based on percentage
     */
    private function calculateGrade($percentage)
    {
        if ($percentage === null) return 'N/A';
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B+';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 50) return 'C+';
        if ($percentage >= 40) return 'C';
        if ($percentage >= 30) return 'D';
        return 'F';
    }
}

