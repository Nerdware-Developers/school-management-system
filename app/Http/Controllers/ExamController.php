<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use App\Models\Student;
use App\Models\Classe;
use Brian2694\Toastr\Facades\Toastr;

class ExamController extends Controller
{
    public function index()
    {
        // Group exams by exam_type, term, and class_id
        $examGroups = DB::table('exams')
            ->leftJoin('classes', 'exams.class_id', '=', 'classes.id')
            ->select(
                'exams.exam_type',
                'exams.term',
                'exams.class_id',
                'classes.class_name',
                DB::raw('MIN(exams.exam_date) as exam_date'),
                DB::raw('COUNT(*) as subject_count'),
                DB::raw('GROUP_CONCAT(DISTINCT exams.subject ORDER BY exams.subject SEPARATOR ", ") as subjects')
            )
            ->whereNotNull('exams.class_id')
            ->whereNotNull('exams.exam_type')
            ->groupBy('exams.exam_type', 'exams.term', 'exams.class_id', 'classes.class_name')
            ->orderByDesc('exams.term')
            ->orderBy('classes.class_name')
            ->orderBy('exams.exam_type')
            ->get();

        // Manual pagination for grouped results
        $currentPage = request()->get('page', 1);
        $perPage = 10;
        $items = $examGroups->forPage($currentPage, $perPage);
        $total = $examGroups->count();
        
        $examGroups = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('exams.list', compact('examGroups'));
    }

    public function addExam()
    {
        $classes = DB::table('classes')->orderBy('class_name')->get();
        return view('exams.add', compact('classes'));
    }

    /**
     * Create exam by type, term, and class (for all subjects)
     */
    public function createExam(Request $request)
    {
        $validated = $request->validate([
            'exam_type'   => 'required|in:mid-term,end-term',
            'term'        => 'required|in:Term 1,Term 2,Term 3',
            'class_id'    => 'required|exists:classes,id',
            'exam_date'   => 'nullable|date',
        ]);

        DB::beginTransaction();
        try {
            $class = Classe::findOrFail($validated['class_id']);
            
            // Get all subjects for this class
            $subjects = Subject::where('class', $class->class_name)->get();
            
            if ($subjects->isEmpty()) {
                Toastr::warning('No subjects found for this class. Please add subjects first.', 'Warning');
                return redirect()->back()->withInput();
            }

            // Create exam for each subject
            $examName = ucfirst($validated['exam_type']) . ' - ' . $validated['term'];
            $createdExams = [];

            foreach ($subjects as $subject) {
                $exam = Exam::create([
                    'exam_name' => $examName . ' - ' . $subject->subject_name,
                    'term' => $validated['term'],
                    'exam_type' => $validated['exam_type'],
                    'class_id' => $validated['class_id'],
                    'subject' => $subject->subject_name,
                    'total_marks' => 100, // Default, can be changed
                    'exam_date' => $validated['exam_date'] ?? null,
                ]);
                $createdExams[] = $exam->id;
            }

            DB::commit();
            Toastr::success(count($createdExams) . ' exams created successfully for all subjects!', 'Success');
            return redirect()->route('exam.enter-marks', [
                'exam_type' => $validated['exam_type'],
                'term' => $validated['term'],
                'class_id' => $validated['class_id']
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error('Failed to create exams: ' . $e->getMessage(), 'Error');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Enter marks for all subjects for a class
     */
    public function enterMarks(Request $request)
    {
        $examType = $request->query('exam_type');
        $term = $request->query('term');
        $classId = $request->query('class_id');

        $classes = Classe::orderBy('class_name')->get();
        $examTypes = ['mid-term' => 'Mid-Term', 'end-term' => 'End-Term'];
        $terms = ['Term 1', 'Term 2', 'Term 3'];

        // Initialize variables to avoid undefined variable errors
        $class = null;
        $exams = collect();
        $students = collect();
        $results = collect();

        if (!$examType || !$term || !$classId) {
            return view('exams.enter-marks', compact('classes', 'examTypes', 'terms', 'examType', 'term', 'classId', 'class', 'exams', 'students', 'results'));
        }

        $class = Classe::findOrFail($classId);
        
        // Get all exams for this exam_type, term, and class
        $exams = Exam::where('exam_type', $examType)
            ->where('term', $term)
            ->where('class_id', $classId)
            ->orderBy('subject')
            ->get();

        if ($exams->isEmpty()) {
            Toastr::warning('No exams found. Please create exams first.', 'Warning');
            return view('exams.enter-marks', compact('classes', 'examTypes', 'terms', 'examType', 'term', 'classId', 'class', 'exams', 'students', 'results'));
        }

        // Get all students in this class
        $students = Student::where('class', $class->class_name)
            ->orderByRaw('CAST(roll as UNSIGNED) asc')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        // Get existing results
        $results = DB::table('exam_results')
            ->whereIn('exam_id', $exams->pluck('id'))
            ->get()
            ->keyBy(function ($result) {
                return $result->exam_id . '_' . $result->student_id;
            });

        return view('exams.enter-marks', compact(
            'classes', 
            'examTypes', 
            'terms', 
            'examType', 
            'term', 
            'classId', 
            'class',
            'exams',
            'students',
            'results'
        ));
    }

    /**
     * Save marks for all subjects
     */
    public function saveMarks(Request $request)
    {
        $validated = $request->validate([
            'exam_type' => 'required|in:mid-term,end-term',
            'term' => 'required|in:Term 1,Term 2,Term 3',
            'class_id' => 'required|exists:classes,id',
            'marks' => 'required|array',
            'marks.*.*' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $now = now();
            $savedCount = 0;

            foreach ($validated['marks'] as $examId => $studentMarks) {
                foreach ($studentMarks as $studentId => $marks) {
                    if ($marks === null || $marks === '') {
                        continue;
                    }

                    DB::table('exam_results')->updateOrInsert(
                        [
                            'exam_id' => $examId,
                            'student_id' => $studentId
                        ],
                        [
                            'marks' => (float)$marks,
                            'updated_at' => $now,
                            'created_at' => DB::raw('COALESCE(created_at, NOW())')
                        ]
                    );
                    $savedCount++;
                }
            }

            DB::commit();
            Toastr::success("Successfully saved {$savedCount} marks!", 'Success');
            return redirect()->route('exam.enter-marks', [
                'exam_type' => $validated['exam_type'],
                'term' => $validated['term'],
                'class_id' => $validated['class_id']
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error('Failed to save marks: ' . $e->getMessage(), 'Error');
            return redirect()->back()->withInput();
        }
    }

    public function edit($id)
    {
        $exam = Exam::findOrFail($id);
        $classes = DB::table('classes')->orderBy('class_name')->get();
        return view('exams.edit', compact('exam', 'classes'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'exam_name'   => 'required|string|max:255',
            'term'        => 'required|string|max:50',
            'exam_type'   => 'nullable|in:mid-term,end-term',
            'class_id'    => 'required|exists:classes,id',
            'subject'     => 'required|string|max:255',
            'total_marks' => 'nullable|integer|min:1',
            'exam_date'   => 'nullable|date',
        ]);

        $exam = Exam::findOrFail($id);
        $exam->update($validated);

        Toastr::success('Exam updated successfully!', 'Success');
        return redirect()->route('exams.page');
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $exam = Exam::findOrFail($id);
            
            // Delete related exam results
            DB::table('exam_results')->where('exam_id', $id)->delete();
            
            // Delete exam
            $exam->delete();

            DB::commit();
            Toastr::success('Exam deleted successfully!', 'Success');
            return redirect()->route('exams.page');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error($e);
            Toastr::error('Failed to delete exam: ' . $e->getMessage(), 'Error');
            return redirect()->back();
        }
    }

    /**
     * View exam results for a specific exam group
     */
    public function viewResults(Request $request)
    {
        $examType = $request->query('exam_type');
        $term = $request->query('term');
        $classId = $request->query('class_id');

        if (!$examType || !$term || !$classId) {
            Toastr::error('Invalid exam parameters.', 'Error');
            return redirect()->route('exams.page');
        }

        $class = Classe::findOrFail($classId);
        
        // Get all exams for this exam_type, term, and class
        $exams = Exam::where('exam_type', $examType)
            ->where('term', $term)
            ->where('class_id', $classId)
            ->orderBy('subject')
            ->get();

        if ($exams->isEmpty()) {
            Toastr::warning('No exams found for the selected criteria.', 'Warning');
            return redirect()->route('exams.page');
        }

        // Get all students in this class
        $students = Student::where('class', $class->class_name)
            ->orderBy('roll')
            ->orderBy('first_name')
            ->get();

        // Get all exam results for these exams
        $examIds = $exams->pluck('id');
        $results = DB::table('exam_results')
            ->whereIn('exam_id', $examIds)
            ->get()
            ->keyBy(function ($result) {
                return $result->exam_id . '_' . $result->student_id;
            });

        // Calculate totals and percentages for each student
        $studentTotals = [];
        foreach ($students as $student) {
            $totalMarks = 0;
            $totalPossible = 0;
            $subjectCount = 0;

            foreach ($exams as $exam) {
                $resultKey = $exam->id . '_' . $student->id;
                $result = $results->get($resultKey);
                
                if ($result && $result->marks !== null) {
                    $totalMarks += $result->marks;
                    $subjectCount++;
                }
                $totalPossible += $exam->total_marks ?? 100;
            }

            $percentage = $totalPossible > 0 ? ($totalMarks / $totalPossible) * 100 : 0;
            
            // Calculate grade
            $grade = $this->calculateGrade($percentage);

            $studentTotals[$student->id] = [
                'total_marks' => $totalMarks,
                'total_possible' => $totalPossible,
                'percentage' => $percentage,
                'grade' => $grade,
                'subject_count' => $subjectCount,
            ];
        }

        return view('exams.view-results', compact(
            'examType',
            'term',
            'class',
            'exams',
            'students',
            'results',
            'studentTotals'
        ));
    }

    /**
     * Calculate grade based on percentage
     */
    private function calculateGrade($percentage)
    {
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B+';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 50) return 'C+';
        if ($percentage >= 40) return 'C';
        if ($percentage >= 30) return 'D';
        return 'F';
    }

    /**
     * Delete all exams for a specific exam_type, term, and class
     */
    public function deleteGroup(Request $request)
    {
        $validated = $request->validate([
            'exam_type' => 'required|in:mid-term,end-term',
            'term' => 'required|string',
            'class_id' => 'required|exists:classes,id',
        ]);

        DB::beginTransaction();
        try {
            // Get all exams for this group
            $exams = Exam::where('exam_type', $validated['exam_type'])
                ->where('term', $validated['term'])
                ->where('class_id', $validated['class_id'])
                ->get();

            if ($exams->isEmpty()) {
                Toastr::warning('No exams found to delete.', 'Warning');
                return redirect()->back();
            }

            $examIds = $exams->pluck('id');

            // Delete all related exam results
            DB::table('exam_results')->whereIn('exam_id', $examIds)->delete();

            // Delete all exams
            Exam::whereIn('id', $examIds)->delete();

            DB::commit();
            Toastr::success(count($exams) . ' exams deleted successfully!', 'Success');
            return redirect()->route('exams.page');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error($e);
            Toastr::error('Failed to delete exams: ' . $e->getMessage(), 'Error');
            return redirect()->back();
        }
    }

    // Legacy methods for backward compatibility
    public function ExamList()
    {
        return $this->index();
    }

    public function ExamAdd()
    {
        return $this->addExam();
    }

    public function resultsEntry(Request $request)
    {
        return $this->enterMarks($request);
    }

    public function resultsSave(Request $request)
    {
        return $this->saveMarks($request);
    }
}
