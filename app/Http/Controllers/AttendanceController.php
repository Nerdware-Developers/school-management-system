<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\Classe;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Show attendance marking page
     */
    public function index(Request $request)
    {
        $classes = Classe::orderBy('class_name')->get();
        $selectedClassId = $request->query('class_id');
        $selectedDate = $request->query('date', date('Y-m-d'));

        $students = collect();
        $attendances = collect();

        if ($selectedClassId && $selectedDate) {
            $classe = Classe::findOrFail($selectedClassId);
            $students = Student::where('class', $classe->class_name)
                ->orderBy('roll')
                ->orderBy('first_name')
                ->get();

            // Get existing attendance for this date
            $attendances = Attendance::where('class_id', $selectedClassId)
                ->whereDate('attendance_date', $selectedDate)
                ->get()
                ->keyBy('student_id');
        }

        return view('attendance.index', compact(
            'classes',
            'selectedClassId',
            'selectedDate',
            'students',
            'attendances'
        ));
    }

    /**
     * Save attendance
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'attendance_date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.student_id' => 'required|exists:students,id',
            'attendance.*.status' => 'required|in:present,absent,late,excused',
            'attendance.*.notes' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $markedCount = 0;

            foreach ($validated['attendance'] as $attendanceData) {
                Attendance::updateOrCreate(
                    [
                        'student_id' => $attendanceData['student_id'],
                        'attendance_date' => $validated['attendance_date'],
                    ],
                    [
                        'class_id' => $validated['class_id'],
                        'status' => $attendanceData['status'],
                        'notes' => $attendanceData['notes'] ?? null,
                        'marked_by' => auth()->user()->name ?? 'System',
                    ]
                );
                $markedCount++;
            }

            DB::commit();
            Toastr::success("Attendance marked for {$markedCount} students!", 'Success');
            return redirect()->route('attendance.index', [
                'class_id' => $validated['class_id'],
                'date' => $validated['attendance_date']
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error('Failed to save attendance: ' . $e->getMessage(), 'Error');
            return redirect()->back()->withInput();
        }
    }

    /**
     * View attendance reports
     */
    public function reports(Request $request)
    {
        $classes = Classe::orderBy('class_name')->get();
        $selectedClassId = $request->query('class_id');
        $startDate = $request->query('start_date', date('Y-m-01'));
        $endDate = $request->query('end_date', date('Y-m-d'));

        $attendanceStats = collect();

        if ($selectedClassId) {
            $classe = Classe::findOrFail($selectedClassId);
            $students = Student::where('class', $classe->class_name)
                ->orderBy('roll')
                ->orderBy('first_name')
                ->get();

            foreach ($students as $student) {
                $stats = Attendance::where('student_id', $student->id)
                    ->whereBetween('attendance_date', [$startDate, $endDate])
                    ->selectRaw('
                        COUNT(*) as total_days,
                        SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present,
                        SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent,
                        SUM(CASE WHEN status = "late" THEN 1 ELSE 0 END) as late,
                        SUM(CASE WHEN status = "excused" THEN 1 ELSE 0 END) as excused
                    ')
                    ->first();

                $attendanceStats->push([
                    'student' => $student,
                    'stats' => $stats,
                    'attendance_rate' => $stats && $stats->total_days > 0 
                        ? (($stats->present + $stats->excused) / $stats->total_days) * 100 
                        : 0,
                ]);
            }
        }

        return view('attendance.reports', compact(
            'classes',
            'selectedClassId',
            'startDate',
            'endDate',
            'attendanceStats'
        ));
    }
}

