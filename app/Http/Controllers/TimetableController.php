<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Timetable;
use App\Models\Classe;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;

class TimetableController extends Controller
{
    /**
     * Show timetable list
     */
    public function index(Request $request)
    {
        $classes = Classe::orderBy('class_name')->get();
        $selectedClassId = $request->query('class_id', $classes->first()?->id);

        $timetable = collect();
        if ($selectedClassId) {
            $timetable = Timetable::where('class_id', $selectedClassId)
                ->with(['subject', 'teacher', 'classe'])
                ->orderByRaw("FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday')")
                ->orderBy('period_number')
                ->get()
                ->groupBy('day');
        }

        return view('timetable.index', compact('classes', 'selectedClassId', 'timetable'));
    }

    /**
     * Show create timetable form
     */
    public function create(Request $request)
    {
        $classes = Classe::orderBy('class_name')->get();
        $selectedClassId = $request->query('class_id');

        if ($selectedClassId) {
            $classe = Classe::findOrFail($selectedClassId);
            $subjects = Subject::where('class', $classe->class_name)->get();
            $teachers = Teacher::all();
        } else {
            $subjects = collect();
            $teachers = collect();
        }

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $periods = [
            ['number' => 1, 'name' => 'Period 1', 'start' => '08:00', 'end' => '09:00'],
            ['number' => 2, 'name' => 'Period 2', 'start' => '09:00', 'end' => '10:00'],
            ['number' => 3, 'name' => 'Period 3', 'start' => '10:00', 'end' => '11:00'],
            ['number' => 4, 'name' => 'Period 4', 'start' => '11:00', 'end' => '12:00'],
            ['number' => 5, 'name' => 'Period 5', 'start' => '12:00', 'end' => '13:00'],
            ['number' => 6, 'name' => 'Period 6', 'start' => '13:00', 'end' => '14:00'],
            ['number' => 7, 'name' => 'Period 7', 'start' => '14:00', 'end' => '15:00'],
            ['number' => 8, 'name' => 'Period 8', 'start' => '15:00', 'end' => '16:00'],
        ];

        return view('timetable.create', compact('classes', 'selectedClassId', 'subjects', 'teachers', 'days', 'periods'));
    }

    /**
     * Store timetable
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'timetable' => 'required|array',
            'timetable.*.day' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday',
            'timetable.*.period_number' => 'required|integer|min:1',
            'timetable.*.subject_id' => 'nullable|exists:subjects,id',
            'timetable.*.teacher_id' => 'nullable|exists:teachers,id',
            'timetable.*.start_time' => 'required|date_format:H:i',
            'timetable.*.end_time' => 'required|date_format:H:i',
            'timetable.*.room' => 'nullable|string|max:50',
        ]);

        DB::beginTransaction();
        try {
            // Delete existing timetable for this class
            Timetable::where('class_id', $validated['class_id'])->delete();

            foreach ($validated['timetable'] as $entry) {
                // Skip if no subject selected
                if (empty($entry['subject_id'])) {
                    continue;
                }

                // Validate end time is after start time
                if (strtotime($entry['end_time']) <= strtotime($entry['start_time'])) {
                    throw new \Exception("End time must be after start time for {$entry['day']} Period {$entry['period_number']}");
                }

                // Check for teacher time collisions if teacher is assigned
                if (!empty($entry['teacher_id'])) {
                    $collisions = Timetable::where('teacher_id', $entry['teacher_id'])
                        ->where('day', $entry['day'])
                        ->where('class_id', '!=', $validated['class_id']) // Different class
                        ->where(function ($query) use ($entry) {
                            // Check for time overlap
                            $query->where(function ($q) use ($entry) {
                                // New start time is within existing period
                                $q->where('start_time', '<=', $entry['start_time'])
                                  ->where('end_time', '>', $entry['start_time']);
                            })->orWhere(function ($q) use ($entry) {
                                // New end time is within existing period
                                $q->where('start_time', '<', $entry['end_time'])
                                  ->where('end_time', '>=', $entry['end_time']);
                            })->orWhere(function ($q) use ($entry) {
                                // New period completely contains existing period
                                $q->where('start_time', '>=', $entry['start_time'])
                                  ->where('end_time', '<=', $entry['end_time']);
                            });
                        })
                        ->with(['classe', 'subject'])
                        ->get();

                    if ($collisions->count() > 0) {
                        $collision = $collisions->first();
                        throw new \Exception(
                            "Teacher collision detected! The teacher already has a lesson in " .
                            "{$collision->classe->class_name} ({$collision->subject->subject_name}) " .
                            "on {$entry['day']} at {$collision->start_time->format('H:i')}-{$collision->end_time->format('H:i')}"
                        );
                    }
                }

                Timetable::create([
                    'class_id' => $validated['class_id'],
                    'subject_id' => $entry['subject_id'],
                    'teacher_id' => $entry['teacher_id'] ?? null,
                    'day' => $entry['day'],
                    'period_number' => $entry['period_number'],
                    'period' => 'Period ' . $entry['period_number'],
                    'start_time' => $entry['start_time'],
                    'end_time' => $entry['end_time'],
                    'room' => $entry['room'] ?? null,
                ]);
            }

            DB::commit();
            Toastr::success('Timetable created successfully!', 'Success');
            return redirect()->route('timetable.index', ['class_id' => $validated['class_id']]);
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error('Failed to create timetable: ' . $e->getMessage(), 'Error');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Delete timetable
     */
    public function destroy($classId)
    {
        try {
            Timetable::where('class_id', $classId)->delete();
            Toastr::success('Timetable deleted successfully!', 'Success');
        } catch (\Exception $e) {
            Toastr::error('Failed to delete timetable: ' . $e->getMessage(), 'Error');
        }
        return redirect()->route('timetable.index');
    }

    /**
     * Get teacher assigned to a subject for a specific class
     */
    public function getTeacherForSubject(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:classes,id',
        ]);

        $assignment = \App\Models\TeacherSubjectClass::where('subject_id', $request->subject_id)
            ->where('class_id', $request->class_id)
            ->with('teacher')
            ->first();

        if ($assignment && $assignment->teacher) {
            return response()->json([
                'success' => true,
                'teacher_id' => $assignment->teacher->id,
                'teacher_name' => $assignment->teacher->full_name,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No teacher assigned to this subject for this class',
        ]);
    }

    /**
     * Check if teacher has a time collision
     */
    public function checkTeacherCollision(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'day' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'class_id' => 'required|exists:classes,id',
            'exclude_period' => 'nullable|integer', // Period number to exclude (for editing)
        ]);

        $collisions = Timetable::where('teacher_id', $request->teacher_id)
            ->where('day', $request->day)
            ->where(function ($query) use ($request) {
                // Check for time overlap
                $query->where(function ($q) use ($request) {
                    // New start time is within existing period
                    $q->where('start_time', '<=', $request->start_time)
                      ->where('end_time', '>', $request->start_time);
                })->orWhere(function ($q) use ($request) {
                    // New end time is within existing period
                    $q->where('start_time', '<', $request->end_time)
                      ->where('end_time', '>=', $request->end_time);
                })->orWhere(function ($q) use ($request) {
                    // New period completely contains existing period
                    $q->where('start_time', '>=', $request->start_time)
                      ->where('end_time', '<=', $request->end_time);
                });
            })
            ->where('class_id', '!=', $request->class_id); // Different class

        // Exclude current period if editing
        if ($request->filled('exclude_period')) {
            $collisions->where('period_number', '!=', $request->exclude_period);
        }

        $collision = $collisions->with(['classe', 'subject'])->first();

        if ($collision) {
            return response()->json([
                'has_collision' => true,
                'message' => "Teacher already has a lesson in {$collision->classe->class_name} ({$collision->subject->subject_name}) at this time",
                'collision' => [
                    'class' => $collision->classe->class_name,
                    'subject' => $collision->subject->subject_name,
                    'day' => $collision->day,
                    'time' => $collision->start_time->format('H:i') . ' - ' . $collision->end_time->format('H:i'),
                ],
            ]);
        }

        return response()->json([
            'has_collision' => false,
        ]);
    }
}

