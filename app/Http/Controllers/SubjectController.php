<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use DB;
use App\Models\Subject;
use Brian2694\Toastr\Facades\Toastr;
use App\Models\Teacher;
use App\Models\Classe;

class SubjectController extends Controller
{
    /** index page */
    public function subjectList(Request $request)
    {
        $query = Subject::with(['teachingAssignments.teacher', 'teachingAssignments.class']);

        // Filter by ID
        if ($request->filled('id')) {
            $query->where('subject_id', 'LIKE', '%' . $request->id . '%');
        }

        // Filter by name
        if ($request->filled('name')) {
            $query->where('subject_name', 'LIKE', '%' . $request->name . '%');
        }

        // Filter by class
        if ($request->filled('class')) {
            $query->where('class', 'LIKE', '%' . $request->class . '%');
        }

        $subjectList = $query->orderBy('subject_id', 'desc')->paginate(10)->withQueryString();
        return view('subjects.subject_list',compact('subjectList'));
    }

    /** subject add */
    public function subjectAdd()
{
    $teachers = Teacher::all();
    $classes = Classe::all();

    return view('subjects.subject_add', compact('teachers', 'classes'));
}


    /** save record */
    public function saveRecord(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'subject_name' => 'required|string|max:255',
                'teacher_name' => 'nullable|string|max:255',
                'class' => 'required|string|max:255',
            ], [
                'subject_name.required' => 'Subject name is required',
                'class.required' => 'Class is required',
            ]);

            $subject = Subject::create([
                'subject_name'  => $request->subject_name,
                'teacher_name'  => $request->teacher_name ?? null,
                'class'         => $request->class,  
            ]);

            // If teacher is assigned, create entry in teacher_subject_class pivot table
            if (!empty($request->teacher_name)) {
                $teacher = Teacher::where('full_name', $request->teacher_name)->first();
                $classe = Classe::where('class_name', $request->class)->first();
                
                if ($teacher && $classe) {
                    // Check if another teacher is already assigned to this subject-class combination
                    $existingAssignment = DB::table('teacher_subject_class')
                        ->where('subject_id', $subject->id)
                        ->where('class_id', $classe->id)
                        ->first();
                    
                    if ($existingAssignment && $existingAssignment->teacher_id != $teacher->id) {
                        $existingTeacher = Teacher::find($existingAssignment->teacher_id);
                        $existingTeacherName = $existingTeacher ? $existingTeacher->full_name : 'Unknown Teacher';
                        
                        DB::rollback();
                        Toastr::error(
                            "Cannot assign teacher: {$existingTeacherName} is already assigned to {$request->subject_name} for {$request->class}. Only one teacher can be assigned per subject-class combination.",
                            'Assignment Conflict'
                        );
                        return redirect()->back()->withInput();
                    }
                    
                    // Check if assignment already exists for this teacher
                    $exists = DB::table('teacher_subject_class')
                        ->where('teacher_id', $teacher->id)
                        ->where('subject_id', $subject->id)
                        ->where('class_id', $classe->id)
                        ->exists();
                    
                    if (!$exists) {
                        try {
                            DB::table('teacher_subject_class')->insert([
                                'teacher_id' => $teacher->id,
                                'subject_id' => $subject->id,
                                'class_id' => $classe->id,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        } catch (\Exception $e) {
                            if (str_contains($e->getMessage(), 'Duplicate entry') || str_contains($e->getMessage(), 'unique_subject_class')) {
                                DB::rollback();
                                Toastr::error(
                                    "Cannot assign teacher: Another teacher is already assigned to {$request->subject_name} for {$request->class}. Only one teacher can be assigned per subject-class combination.",
                                    'Assignment Conflict'
                                );
                                return redirect()->back()->withInput();
                            } else {
                                throw $e;
                            }
                        }
                    }
                }
            }

            Toastr::success('Subject added successfully :)','Success');
            DB::commit();
            return redirect()->route('subject/list/page');
            
        } catch(ValidationException $e) {
            DB::rollback();
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch(\Exception $e) {
            \Log::info($e);
            DB::rollback();
            Toastr::error('Fail, add subject :)','Error');
            return redirect()->back();
        }
    }



    /** subject edit view */
    public function subjectEdit($subject_id)
    {
        try {
            $subjectEdit = Subject::with(['teachingAssignments.teacher', 'teachingAssignments.class'])
                ->where('subject_id', $subject_id)->firstOrFail();
            
            // Get current teacher from pivot table if exists
            $currentAssignment = $subjectEdit->teachingAssignments->first();
            if ($currentAssignment && $currentAssignment->teacher) {
                $subjectEdit->current_teacher_name = $currentAssignment->teacher->full_name;
            } else {
                $subjectEdit->current_teacher_name = $subjectEdit->teacher_name ?? '';
            }
            
            $teachers = Teacher::all();
            $classes = Classe::all();
            return view('subjects.subject_edit', compact('subjectEdit', 'teachers', 'classes'));
        } catch (\Exception $e) {
            Toastr::error('Subject not found', 'Error');
            return redirect()->route('subject/list/page');
        }
    }

    /** update record */
    public function updateRecord(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'subject_name' => 'required|string|max:255',
                'teacher_name' => 'nullable|string|max:255',
                'class' => 'required|string|max:255',
            ], [
                'subject_name.required' => 'Subject name is required',
                'class.required' => 'Class is required',
            ]);
            
            $subject = Subject::where('subject_id', $request->subject_id)->firstOrFail();
            
            $updateRecord = [
                'subject_name' => $request->subject_name,
                'teacher_name' => $request->teacher_name ?? null,
                'class'        => $request->class,
            ];

            $subject->update($updateRecord);

            // Update teacher_subject_class pivot table
            $classe = Classe::where('class_name', $request->class)->first();
            
            if ($classe) {
                // If teacher is assigned, check for conflicts before updating
                if (!empty($request->teacher_name)) {
                    $teacher = Teacher::where('full_name', $request->teacher_name)->first();
                    
                    if ($teacher) {
                        // Check if another teacher is already assigned to this subject-class combination
                        // (excluding the current subject record if it has an existing assignment)
                        $existingAssignment = DB::table('teacher_subject_class')
                            ->where('subject_id', $subject->id)
                            ->where('class_id', $classe->id)
                            ->first();
                        
                        // If there's an existing assignment for this subject-class combo with a different teacher
                        if ($existingAssignment && $existingAssignment->teacher_id != $teacher->id) {
                            $existingTeacher = Teacher::find($existingAssignment->teacher_id);
                            $existingTeacherName = $existingTeacher ? $existingTeacher->full_name : 'Unknown Teacher';
                            
                            DB::rollback();
                            Toastr::error(
                                "Cannot update: {$existingTeacherName} is already assigned to {$request->subject_name} for {$request->class}. Only one teacher can be assigned per subject-class combination.",
                                'Assignment Conflict'
                            );
                            return redirect()->back()->withInput();
                        }
                    }
                }
                
                // Only remove assignments for this specific subject and class combination
                // This preserves assignments for other classes if the subject is taught in multiple classes
                DB::table('teacher_subject_class')
                    ->where('subject_id', $subject->id)
                    ->where('class_id', $classe->id)
                    ->delete();

                // If teacher is assigned, create new entry in pivot table
                if (!empty($request->teacher_name)) {
                    $teacher = Teacher::where('full_name', $request->teacher_name)->first();
                    
                    if ($teacher) {
                        try {
                            DB::table('teacher_subject_class')->insert([
                                'teacher_id' => $teacher->id,
                                'subject_id' => $subject->id,
                                'class_id' => $classe->id,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        } catch (\Exception $e) {
                            if (str_contains($e->getMessage(), 'Duplicate entry') || str_contains($e->getMessage(), 'unique_subject_class')) {
                                DB::rollback();
                                Toastr::error(
                                    "Cannot update: Another teacher is already assigned to {$request->subject_name} for {$request->class}. Only one teacher can be assigned per subject-class combination.",
                                    'Assignment Conflict'
                                );
                                return redirect()->back()->withInput();
                            } else {
                                throw $e;
                            }
                        }
                    }
                }
            }

            Toastr::success('Has been update successfully :)','Success');
            DB::commit();
            return redirect()->route('subject/list/page');
           
        } catch(ValidationException $e) {
            DB::rollback();
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch(\Exception $e) {
            \Log::info($e);
            DB::rollback();
            Toastr::error('Fail, update record:)','Error');
            return redirect()->back();
        }
    }

    /** delete record */
    public function deleteRecord(Request $request)
    {
        DB::beginTransaction();
        try {
            $subject = Subject::where('subject_id', $request->subject_id)->firstOrFail();
            
            // Delete related pivot table entries (cascade should handle this, but being explicit)
            DB::table('teacher_subject_class')
                ->where('subject_id', $subject->id)
                ->delete();
            
            // Delete the subject
            $subject->delete();
            
            DB::commit();
            Toastr::success('Deleted record successfully :)','Success');
            return redirect()->back();
        } catch(\Exception $e) {
            DB::rollback();
            \Log::error('Subject delete failed: ' . $e->getMessage());
            Toastr::error('Deleted record fail :)','Error');
            return redirect()->back();
        }
    }

    /** bulk delete records */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'subject_ids' => 'required|array',
            'subject_ids.*' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $subjectIds = $request->subject_ids;
            
            // Get actual database IDs from subject_id strings
            $subjects = Subject::whereIn('subject_id', $subjectIds)->get();
            $dbIds = $subjects->pluck('id')->toArray();
            
            if (empty($dbIds)) {
                DB::rollback();
                return response()->json([
                    'success' => false,
                    'message' => 'No subjects found to delete'
                ], 404);
            }
            
            // Delete related pivot table entries
            DB::table('teacher_subject_class')
                ->whereIn('subject_id', $dbIds)
                ->delete();
            
            // Delete the subjects
            $deletedCount = Subject::whereIn('id', $dbIds)->delete();
            
            DB::commit();
            Toastr::success("Successfully deleted {$deletedCount} subject(s)", 'Success');
            return response()->json([
                'success' => true,
                'message' => "Successfully deleted {$deletedCount} subject(s)",
                'deleted_count' => $deletedCount
            ]);
        } catch(\Exception $e) {
            DB::rollback();
            \Log::error('Bulk delete error: ' . $e->getMessage());
            Toastr::error('Failed to delete subjects', 'Error');
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete subjects: ' . $e->getMessage()
            ], 500);
        }
    }

}
