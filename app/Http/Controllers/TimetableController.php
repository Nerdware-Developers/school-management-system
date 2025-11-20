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
                ->orderByRaw("FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')")
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

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
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
            'timetable.*.day' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
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
}

