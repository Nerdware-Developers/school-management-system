<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Hash;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Classe;
use App\Models\Subject;
use App\Models\SalaryPayment;
use Brian2694\Toastr\Facades\Toastr;

class TeacherController extends Controller
{
    /** add teacher page */
    public function teacherAdd()
    {
        $users = User::where('role_name','Teachers')->get();
        $classes = Classe::all();
        $subjects = Subject::all();
        return view('teacher.add-teacher',compact('users', 'classes', 'subjects'));
    }

    /** teacher list */
    public function teacherList(Request $request)
    {
        $query = Teacher::with(['teachingAssignments.subject', 'teachingAssignments.class', 'classTeacher']);

        // Filter by ID (user_id)
        if ($request->filled('id')) {
            $query->where('user_id', 'LIKE', '%' . $request->id . '%');
        }

        // Filter by name
        if ($request->filled('name')) {
            $search = trim($request->name);
            $query->where('full_name', 'LIKE', '%' . $search . '%');
        }

        // Filter by phone
        if ($request->filled('phone')) {
            $query->where('phone_number', 'LIKE', '%' . $request->phone . '%');
        }

        $listTeacher = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();
        return view('teacher.list-teachers', compact('listTeacher'));
    }

    /** teacher profiles list */
    public function teacherProfiles()
{
    $teachers = Teacher::with(['classTeacher', 'teachingAssignments.subject', 'teachingAssignments.class'])
        ->orderBy('id', 'desc')
        ->paginate(12);
    return view('teacher.teacher-profiles', compact('teachers'));
}





    /** teacher Grid */
    public function teacherGrid()
    {
        $teacherGrid = Teacher::all();
        return view('teacher.teachers-grid',compact('teacherGrid'));
    }

    /** save record */
    public function saveRecord(Request $request)
    {
        $request->validate([
            'full_name'     => 'required|string',
            'gender'        => 'required|string',
            'experience'    => 'required|string',
            'date_of_birth' => 'required|string',
            'qualification' => 'required|string',
            'phone_number'  => 'required|string',
            'address'       => 'required|string',
            'city'          => 'required|string',
            'state'         => 'required|string',
            'zip_code'      => 'required|string',
            'country'       => 'required|string',
            'monthly_salary' => 'nullable|numeric|min:0',
            'is_class_teacher' => 'nullable|in:yes,no',
            'class_teacher_id' => 'required_if:is_class_teacher,yes|nullable|exists:classes,id',
            'subject_class' => 'nullable|array',
            'subject_class.*.subject_id' => 'nullable',
            'subject_class.*.class_id' => 'nullable|exists:classes,id',
            'subject_class.*.new_subject_name' => 'nullable|string|max:255',
            'subject_class.*.new_subject_class' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $teacher = new Teacher;
            $teacher->full_name     = $request->full_name;
            $teacher->gender        = $request->gender;
            $teacher->experience    = $request->experience;
            $teacher->qualification = $request->qualification;
            $teacher->date_of_birth = $request->date_of_birth;
            $teacher->phone_number  = $request->phone_number;
            $teacher->address       = $request->address;
            $teacher->city          = $request->city;
            $teacher->state         = $request->state;
            $teacher->zip_code      = $request->zip_code;
            $teacher->country       = $request->country;
            $teacher->monthly_salary = $request->monthly_salary ?? null;
            
            // Set class teacher if provided
            if ($request->is_class_teacher == 'yes' && $request->class_teacher_id) {
                // Check if another teacher is already assigned as class teacher for this class
                $existingClassTeacher = Teacher::where('class_teacher_id', $request->class_teacher_id)
                    ->where('id', '!=', $teacher->id ?? 0) // Exclude current teacher if updating
                    ->first();
                
                if ($existingClassTeacher) {
                    $classe = Classe::find($request->class_teacher_id);
                    $className = $classe ? $classe->class_name : 'Unknown Class';
                    DB::rollback();
                    Toastr::error(
                        "Cannot assign as class teacher: {$existingClassTeacher->full_name} is already assigned as class teacher for {$className}. Only one teacher can be assigned as class teacher per class.",
                        'Assignment Conflict'
                    );
                    return redirect()->back()->withInput();
                }
                
                $teacher->class_teacher_id = $request->class_teacher_id;
            }

            // Generate a teacher_id automatically
            $teacher->user_id = 'T' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

            try {
                $teacher->save();
            } catch (\Exception $e) {
                // Handle unique constraint violation for class_teacher_id
                if (str_contains($e->getMessage(), 'Duplicate entry') || 
                    str_contains($e->getMessage(), 'unique_class_teacher') ||
                    str_contains($e->getMessage(), '1062')) {
                    DB::rollback();
                    if ($request->is_class_teacher == 'yes' && $request->class_teacher_id) {
                        $classe = Classe::find($request->class_teacher_id);
                        $className = $classe ? $classe->class_name : 'Unknown Class';
                        $existingTeacher = Teacher::where('class_teacher_id', $request->class_teacher_id)->first();
                        $existingTeacherName = $existingTeacher ? $existingTeacher->full_name : 'Another teacher';
                        Toastr::error(
                            "Cannot assign as class teacher: {$existingTeacherName} is already assigned as class teacher for {$className}. Only one teacher can be assigned as class teacher per class.",
                            'Assignment Conflict'
                        );
                    } else {
                        Toastr::error('Failed to save teacher: ' . $e->getMessage(), 'Error');
                    }
                    return redirect()->back()->withInput();
                }
                throw $e;
            }

            // Save subject-class assignments
            $assignmentsSaved = 0;
            $newSubjectsCreated = 0;
            if ($request->has('subject_class') && is_array($request->subject_class)) {
                foreach ($request->subject_class as $index => $assignment) {
                    try {
                        $subjectId = null;
                        $classId = null;
                        
                        // Check if this is a new subject to create
                        if (!empty($assignment['subject_id']) && $assignment['subject_id'] === '__new__') {
                            // Validate new subject data
                            if (empty($assignment['new_subject_name']) || empty($assignment['new_subject_class'])) {
                                \Log::warning("Skipping assignment {$index}: New subject name or class missing");
                                continue;
                            }
                            
                            // Create new subject
                            $classe = Classe::where('class_name', $assignment['new_subject_class'])->first();
                            if (!$classe) {
                                \Log::warning("Skipping assignment {$index}: Class not found: " . $assignment['new_subject_class']);
                                continue;
                            }
                            
                            $newSubject = Subject::create([
                                'subject_name' => $assignment['new_subject_name'],
                                'class' => $assignment['new_subject_class'],
                                'teacher_name' => null, // Will be set via pivot table
                            ]);
                            $subjectId = $newSubject->id;
                            $classId = $classe->id;
                            $newSubjectsCreated++;
                        } else {
                            // Use existing subject
                            if (!empty($assignment['subject_id']) && !empty($assignment['class_id'])) {
                                // Validate that subject exists
                                $subject = Subject::find($assignment['subject_id']);
                                if (!$subject) {
                                    \Log::warning("Skipping assignment {$index}: Subject not found: " . $assignment['subject_id']);
                                    continue;
                                }
                                
                                $subjectId = $assignment['subject_id'];
                                $classId = $assignment['class_id'];
                            }
                        }
                        
                        // Create assignment if we have both subject and class
                        if ($subjectId && $classId) {
                            // Check if this teacher already has this assignment
                            $existsForThisTeacher = DB::table('teacher_subject_class')
                                ->where('teacher_id', $teacher->id)
                                ->where('subject_id', $subjectId)
                                ->where('class_id', $classId)
                                ->exists();
                            
                            // Check if another teacher is already assigned to this subject-class combination
                            $existsForOtherTeacher = DB::table('teacher_subject_class')
                                ->where('subject_id', $subjectId)
                                ->where('class_id', $classId)
                                ->where('teacher_id', '!=', $teacher->id)
                                ->exists();
                            
                            if ($existsForOtherTeacher) {
                                $subjectName = Subject::find($subjectId)->subject_name ?? 'Unknown Subject';
                                $className = Classe::find($classId)->class_name ?? 'Unknown Class';
                                $existingTeacher = Teacher::find(
                                    DB::table('teacher_subject_class')
                                        ->where('subject_id', $subjectId)
                                        ->where('class_id', $classId)
                                        ->value('teacher_id')
                                );
                                $existingTeacherName = $existingTeacher ? $existingTeacher->full_name : 'Unknown Teacher';
                                
                                Toastr::warning(
                                    "Cannot assign: Another teacher ({$existingTeacherName}) is already assigned to {$subjectName} for {$className}. Only one teacher can be assigned per subject-class combination.",
                                    'Assignment Conflict'
                                );
                                continue;
                            }
                            
                            if (!$existsForThisTeacher) {
                                try {
                                    $teacher->subjectClasses()->attach($classId, [
                                        'subject_id' => $subjectId
                                    ]);
                                    $assignmentsSaved++;
                                } catch (\Exception $e) {
                                    if (str_contains($e->getMessage(), 'Duplicate entry') || str_contains($e->getMessage(), 'unique_subject_class')) {
                                        $subjectName = Subject::find($subjectId)->subject_name ?? 'Unknown Subject';
                                        $className = Classe::find($classId)->class_name ?? 'Unknown Class';
                                        Toastr::warning(
                                            "Cannot assign: Another teacher is already assigned to {$subjectName} for {$className}. Only one teacher can be assigned per subject-class combination.",
                                            'Assignment Conflict'
                                        );
                                    } else {
                                        throw $e;
                                    }
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        \Log::warning("Failed to save assignment {$index}: " . $e->getMessage());
                        // Continue with other assignments
                    }
                }
            }

            DB::commit();
            
            $message = 'Teacher has been added successfully';
            if ($newSubjectsCreated > 0) {
                $message .= " ({$newSubjectsCreated} new subject(s) created)";
            }
            if ($assignmentsSaved > 0) {
                $message .= " with {$assignmentsSaved} subject-class assignment(s)";
            }
            Toastr::success($message, 'Success');
            return redirect()->route('teacher/list/page');
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Teacher save failed: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            Toastr::error('Failed to add teacher: ' . $e->getMessage(), 'Error');
            return redirect()->back()->withInput();
        }
    }


    /** edit record */
    public function editRecord($id)
{
    $teacher = Teacher::with(['teachingAssignments.subject', 'teachingAssignments.class', 'classTeacher'])->find($id);

    if (!$teacher) {
        abort(404, 'Teacher not found');
    }

    $classes = Classe::all();
    $subjects = Subject::all();

    return view('teacher.edit-teacher', compact('teacher', 'classes', 'subjects'));
}


    /** update record teacher */
    public function updateRecordTeacher(Request $request)
    {
        $request->validate([
            'full_name'     => 'required|string',
            'gender'        => 'required|string',
            'date_of_birth' => 'required|string',
            'qualification' => 'required|string',
            'experience'    => 'required|string',
            'phone_number'  => 'required|string',
            'address'       => 'required|string',
            'city'          => 'required|string',
            'state'         => 'required|string',
            'zip_code'      => 'required|string',
            'country'       => 'required|string',
            'monthly_salary' => 'nullable|numeric|min:0',
            'is_class_teacher' => 'nullable|in:yes,no',
            'class_teacher_id' => 'required_if:is_class_teacher,yes|nullable|exists:classes,id',
            'subject_class' => 'nullable|array',
            'subject_class.*.subject_id' => 'nullable',
            'subject_class.*.class_id' => 'nullable|exists:classes,id',
            'subject_class.*.new_subject_name' => 'nullable|string|max:255',
            'subject_class.*.new_subject_class' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $teacher = Teacher::findOrFail($request->id);

            $updateRecord = [
                'full_name'     => $request->full_name,
                'gender'        => $request->gender,
                'date_of_birth' => $request->date_of_birth,
                'qualification' => $request->qualification,
                'experience'    => $request->experience,
                'phone_number'  => $request->phone_number,
                'address'       => $request->address,
                'city'          => $request->city,
                'state'         => $request->state,
                'zip_code'      => $request->zip_code,
                'country'      => $request->country,
                'monthly_salary' => $request->monthly_salary ?? null,
            ];

            // Handle class teacher assignment
            if ($request->is_class_teacher == 'yes' && $request->class_teacher_id) {
                // Check if another teacher is already assigned as class teacher for this class
                $existingClassTeacher = Teacher::where('class_teacher_id', $request->class_teacher_id)
                    ->where('id', '!=', $teacher->id)
                    ->first();
                
                if ($existingClassTeacher) {
                    $classe = Classe::find($request->class_teacher_id);
                    $className = $classe ? $classe->class_name : 'Unknown Class';
                    DB::rollback();
                    Toastr::error(
                        "Cannot assign as class teacher: {$existingClassTeacher->full_name} is already assigned as class teacher for {$className}. Only one teacher can be assigned as class teacher per class.",
                        'Assignment Conflict'
                    );
                    return redirect()->back()->withInput();
                }
                
                $updateRecord['class_teacher_id'] = $request->class_teacher_id;
            } else {
                // Remove class teacher assignment
                $updateRecord['class_teacher_id'] = null;
            }

            Teacher::where('id', $request->id)->update($updateRecord);

            // Delete existing subject-class assignments
            DB::table('teacher_subject_class')->where('teacher_id', $teacher->id)->delete();

            // Save new subject-class assignments
            $assignmentsSaved = 0;
            $newSubjectsCreated = 0;
            if ($request->has('subject_class') && is_array($request->subject_class)) {
                foreach ($request->subject_class as $index => $assignment) {
                    try {
                        $subjectId = null;
                        $classId = null;
                        
                        // Skip if both subject_id and class_id are empty
                        if (empty($assignment['subject_id']) && empty($assignment['class_id'])) {
                            continue;
                        }

                        // Check if this is a new subject to create
                        if (!empty($assignment['subject_id']) && $assignment['subject_id'] === '__new__') {
                            // Validate new subject data
                            if (empty($assignment['new_subject_name']) || empty($assignment['new_subject_class'])) {
                                \Log::warning("Skipping assignment {$index}: New subject name or class missing");
                                continue;
                            }
                            
                            // Create new subject
                            $classe = Classe::where('class_name', $assignment['new_subject_class'])->first();
                            if (!$classe) {
                                \Log::warning("Skipping assignment {$index}: Class not found: " . $assignment['new_subject_class']);
                                continue;
                            }
                            
                            $newSubject = Subject::create([
                                'subject_name' => $assignment['new_subject_name'],
                                'class' => $assignment['new_subject_class'],
                                'teacher_name' => null,
                            ]);
                            $subjectId = $newSubject->id;
                            $classId = $classe->id;
                            $newSubjectsCreated++;
                        } else {
                            // Use existing subject
                            if (!empty($assignment['subject_id']) && !empty($assignment['class_id'])) {
                                // Validate that subject exists
                                $subject = Subject::find($assignment['subject_id']);
                                if (!$subject) {
                                    \Log::warning("Skipping assignment {$index}: Subject not found: " . $assignment['subject_id']);
                                    continue;
                                }
                                
                                $subjectId = $assignment['subject_id'];
                                $classId = $assignment['class_id'];
                            }
                        }
                        
                        // Create assignment if we have both subject and class
                        if ($subjectId && $classId) {
                            // Check if another teacher is already assigned to this subject-class combination
                            $existsForOtherTeacher = DB::table('teacher_subject_class')
                                ->where('subject_id', $subjectId)
                                ->where('class_id', $classId)
                                ->where('teacher_id', '!=', $teacher->id)
                                ->exists();
                            
                            if ($existsForOtherTeacher) {
                                $subjectName = Subject::find($subjectId)->subject_name ?? 'Unknown Subject';
                                $className = Classe::find($classId)->class_name ?? 'Unknown Class';
                                Toastr::warning(
                                    "Cannot assign: Another teacher is already assigned to {$subjectName} for {$className}. Only one teacher can be assigned per subject-class combination.",
                                    'Assignment Conflict'
                                );
                                continue;
                            }
                            
                            try {
                                $teacher->subjectClasses()->attach($classId, [
                                    'subject_id' => $subjectId
                                ]);
                                $assignmentsSaved++;
                            } catch (\Exception $e) {
                                if (str_contains($e->getMessage(), 'Duplicate entry') || str_contains($e->getMessage(), 'unique_subject_class')) {
                                    $subjectName = Subject::find($subjectId)->subject_name ?? 'Unknown Subject';
                                    $className = Classe::find($classId)->class_name ?? 'Unknown Class';
                                    Toastr::warning(
                                        "Cannot assign: Another teacher is already assigned to {$subjectName} for {$className}. Only one teacher can be assigned per subject-class combination.",
                                        'Assignment Conflict'
                                    );
                                } else {
                                    throw $e;
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        \Log::warning("Failed to save assignment {$index}: " . $e->getMessage());
                        // Continue with other assignments
                    }
                }
            }
            
            Toastr::success('Has been update successfully :)','Success');
            if ($newSubjectsCreated > 0 || $assignmentsSaved > 0) {
                $message = 'Teacher updated successfully';
                if ($newSubjectsCreated > 0) {
                    $message .= " ({$newSubjectsCreated} new subject(s) created)";
                }
                if ($assignmentsSaved > 0) {
                    $message .= " with {$assignmentsSaved} subject-class assignment(s)";
                }
                Toastr::success($message, 'Success');
            }
            DB::commit();
            return redirect()->back();
           
        } catch(\Exception $e) {
            DB::rollback();
            \Log::error('Teacher update failed: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            Toastr::error('fail, update record  :)','Error');
            return redirect()->back()->withInput();
        }
    }

    /** delete record */
    public function teacherDelete(Request $request)
    {
        DB::beginTransaction();
        try {

            Teacher::destroy($request->id);
            DB::commit();
            Toastr::success('Deleted record successfully :)','Success');
            return redirect()->back();
        } catch(\Exception $e) {
            DB::rollback();
            \Log::info($e);
            Toastr::error('Deleted record fail :)','Error');
            return redirect()->back();
        }
    }

    /** teacher profile */
    public function teacherProfile($id)
    {
        $teacher = Teacher::with(['classTeacher', 'subjectClasses', 'teachingAssignments.subject', 'teachingAssignments.class'])
            ->findOrFail($id);

        // Get teaching assignments
        $assignments = collect();
        if ($teacher->relationLoaded('teachingAssignments')) {
            $assignments = $teacher->teachingAssignments->map(function ($assignment) {
                return [
                    'subject' => optional($assignment->subject)->subject_name ?? 'N/A',
                    'class' => optional($assignment->class)->class_name ?? 'N/A',
                ];
            });
        }

        // Get unique classes and subjects counts
        $uniqueClasses = $teacher->subjectClasses->pluck('class_name')->unique()->count();
        $uniqueSubjects = $teacher->teachingAssignments->pluck('subject.subject_name')->filter()->unique()->count();

        $stats = [
            'classes' => $uniqueClasses,
            'subjects' => $uniqueSubjects,
        ];

        // Get payment history
        $paymentHistory = SalaryPayment::where('staff_name', $teacher->full_name)
            ->where('role', 'Teacher')
            ->orderBy('payment_date', 'desc')
            ->get();

        // Calculate payment statistics
        $paymentStats = [
            'total_paid' => $paymentHistory->sum('amount'),
            'total_payments' => $paymentHistory->count(),
            'this_year' => $paymentHistory->filter(function ($payment) {
                return $payment->payment_date && $payment->payment_date->year === now()->year;
            })->sum('amount'),
            'this_month' => $paymentHistory->filter(function ($payment) {
                return $payment->payment_date && 
                       $payment->payment_date->year === now()->year && 
                       $payment->payment_date->month === now()->month;
            })->sum('amount'),
        ];

        return view('teacher.teacher-profile', compact(
            'teacher',
            'stats',
            'assignments',
            'paymentHistory',
            'paymentStats'
        ));
    }

    /** bulk delete teachers */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'teacher_ids' => 'required|array',
            'teacher_ids.*' => 'required|integer|exists:teachers,id',
        ]);

        DB::beginTransaction();
        try {
            $teacherIds = $request->teacher_ids;
            $deletedCount = Teacher::whereIn('id', $teacherIds)->delete();
            
            DB::commit();
            Toastr::success("Successfully deleted {$deletedCount} teacher(s)", 'Success');
            return response()->json([
                'success' => true,
                'message' => "Successfully deleted {$deletedCount} teacher(s)",
                'deleted_count' => $deletedCount
            ]);
        } catch(\Exception $e) {
            DB::rollback();
            \Log::error('Bulk delete teachers failed: ' . $e->getMessage());
            Toastr::error('Failed to delete teachers', 'Error');
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete teachers'
            ], 500);
        }
    }

    /** Export teachers to Excel (CSV format) */
    public function exportExcel(Request $request)
    {
        $query = Teacher::with(['teachingAssignments.subject', 'teachingAssignments.class', 'classTeacher']);
        $filters = [];

        // Apply same filters as teacher list
        if ($request->has('id') && $request->id != '') {
            $query->where('user_id', 'LIKE', '%' . $request->id . '%');
            $filters[] = 'id_' . str_replace(' ', '_', $request->id);
        }

        if ($request->has('name') && $request->name != '') {
            $search = trim($request->name);
            $query->where('full_name', 'LIKE', '%' . $search . '%');
            $filters[] = 'name_' . str_replace(' ', '_', $search);
        }

        if ($request->has('phone') && $request->phone != '') {
            $query->where('phone_number', 'LIKE', '%' . $request->phone . '%');
            $filters[] = 'phone_' . str_replace(' ', '_', $request->phone);
        }

        // Get all teachers (no pagination for export)
        $teachers = $query->orderBy('full_name')->get();

        // Set headers for CSV download
        $filterSuffix = !empty($filters) ? '_' . implode('_', $filters) : '';
        $filename = 'teachers' . $filterSuffix . '_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        // Add BOM for UTF-8 to ensure Excel displays special characters correctly
        $callback = function() use ($teachers) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header row
            fputcsv($file, [
                'ID', 'Full Name', 'Gender', 'Date of Birth', 'Phone Number', 'Address',
                'City', 'State', 'Zip Code', 'Country', 'Qualification', 'Experience',
                'Monthly Salary', 'Class Teacher', 'Subjects Taught', 'Classes Taught'
            ]);

            // Data rows
            foreach ($teachers as $teacher) {
                // Get classes taught
                $classes = $teacher->teachingAssignments->map(function($assignment) {
                    return optional($assignment->class)->class_name;
                })->filter()->unique()->values();
                
                // If no classes from assignments, check if teacher is a class teacher
                if ($classes->isEmpty() && $teacher->classTeacher) {
                    $classes = collect([$teacher->classTeacher->class_name]);
                }

                // Get subjects taught
                $subjects = $teacher->teachingAssignments->map(function($assignment) {
                    return optional($assignment->subject)->subject_name;
                })->filter()->unique()->values();

                $classTeacherName = $teacher->classTeacher ? $teacher->classTeacher->class_name : 'N/A';

                // Format date of birth for Excel (as text to prevent formatting issues)
                $dateOfBirth = '';
                if ($teacher->date_of_birth) {
                    try {
                        $date = \Carbon\Carbon::parse($teacher->date_of_birth);
                        $dateOfBirth = "'" . $date->format('Y-m-d'); // Prefix with ' to force text format
                    } catch (\Exception $e) {
                        $dateOfBirth = "'" . $teacher->date_of_birth; // If parsing fails, use as-is with prefix
                    }
                }

                // Format qualification for Excel (as text to prevent display issues)
                $qualification = $teacher->qualification ? "'" . $teacher->qualification : '';

                fputcsv($file, [
                    $teacher->user_id ?? '',
                    $teacher->full_name ?? '',
                    $teacher->gender ?? '',
                    $dateOfBirth,
                    $teacher->phone_number ? "'" . $teacher->phone_number : '', // Prefix with ' for Excel
                    $teacher->address ?? '',
                    $teacher->city ?? '',
                    $teacher->state ?? '',
                    $teacher->zip_code ?? '',
                    $teacher->country ?? '',
                    $qualification,
                    $teacher->experience ?? '',
                    $teacher->monthly_salary ?? '0',
                    $classTeacherName,
                    $subjects->isNotEmpty() ? $subjects->implode(', ') : 'N/A',
                    $classes->isNotEmpty() ? $classes->implode(', ') : 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

}
