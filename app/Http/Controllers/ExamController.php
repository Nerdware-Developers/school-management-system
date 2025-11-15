<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exam;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Student;

class ExamController extends Controller
{
    public function ExamList()
    {
        $exams = DB::table('exams')
            ->leftJoin('classes', 'exams.class_id', '=', 'classes.id')
            ->select('exams.*', 'classes.class_name')
            ->orderByDesc('exams.created_at')
            ->paginate(10);
        return view('exams.list', compact('exams'));
    }

    public function ExamAdd()
    {
        $classes = DB::table('classes')->orderBy('class_name')->get();
        return view('exams.add', compact('classes'));
    }

    public function index()
    {
        $exams = DB::table('exams')
            ->leftJoin('classes', 'exams.class_id', '=', 'classes.id')
            ->select('exams.*', 'classes.class_name')
            ->orderByDesc('exams.created_at')
            ->paginate(10);
        return view('exams.list', compact('exams'));
    }

    public function addExam()
    {
        $classes = DB::table('classes')->orderBy('class_name')->get();
        return view('exams.add', compact('classes'));
    }

    public function saveExam(Request $request)
    {
        $validated = $request->validate([
            'exam_name'   => 'required|string|max:255',
            'term'        => 'required|string|max:50',
            'class_id'    => 'required|exists:classes,id',
            'subject'     => 'required|string|max:255',
            'total_marks' => 'nullable|integer|min:1',
            'exam_date'   => 'nullable|string',
        ]);

        if (!empty($validated['exam_date'])) {
            try {
                $validated['exam_date'] = Carbon::createFromFormat('d-m-Y', $validated['exam_date'])->format('Y-m-d');
            } catch (\Throwable $e) {
                // Fallback: let database accept null if parsing fails
                $validated['exam_date'] = null;
            }
        }

        Exam::create($validated);

        return redirect()->route('exams.page')->with('success', 'Exam created successfully.');
    }

    public function resultsEntry(Request $request)
    {
        $exams = DB::table('exams')->orderByDesc('created_at')->get();
        $classes = DB::table('classes')->orderBy('class_name')->get();

        $selectedExamId = $request->query('exam_id');
        $selectedClassId = $request->query('class_id');

        $students = collect();
        $selectedClass = null;
        if ($selectedClassId) {
            $selectedClass = DB::table('classes')->where('id', $selectedClassId)->first();
            if ($selectedClass) {
                $students = DB::table('students')
                    ->where('class', $selectedClass->class_name)
                    ->orderByRaw('CAST(roll as UNSIGNED) asc')
                    ->orderBy('last_name')
                    ->orderBy('first_name')
                    ->get();
            }
        }

        return view('exams.results_entry', compact(
            'exams',
            'classes',
            'students',
            'selectedExamId',
            'selectedClassId',
            'selectedClass'
        ));
    }

    public function resultsSave(Request $request)
    {
        $validated = $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'class_id' => 'required|exists:classes,id',
            'marks' => 'array',
            'marks.*' => 'nullable|integer|min:0|max:100',
        ]);

        $examId = (int)$validated['exam_id'];
        $classId = (int)$validated['class_id'];

        $now = now();
        foreach ($validated['marks'] ?? [] as $studentId => $marks) {
            if ($marks === null || $marks === '') {
                continue;
            }

            DB::table('exam_results')->updateOrInsert(
                ['exam_id' => $examId, 'student_id' => (int)$studentId],
                ['marks' => (int)$marks, 'updated_at' => $now, 'created_at' => $now]
            );
        }

        return redirect()
            ->route('exam.results.entry', ['exam_id' => $examId, 'class_id' => $classId])
            ->with('success', 'Results saved.');
    }
}
