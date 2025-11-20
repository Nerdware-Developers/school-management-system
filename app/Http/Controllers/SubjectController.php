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
    public function subjectList()
    {
        $subjectList = Subject::orderBy('subject_id', 'desc')->paginate(10);
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
                'teacher_name' => 'required|string|max:255',
                'class' => 'required|string|max:255',
            ], [
                'subject_name.required' => 'Subject name is required',
                'teacher_name.required' => 'Teacher name is required',
                'class.required' => 'Class is required',
            ]);

            Subject::create([
                'subject_name'  => $request->subject_name,
                'teacher_name'  => $request->teacher_name,
                'class'         => $request->class,  
            ]);

            Toastr::success('Subject added successfully :)','Success');
            DB::commit();
            return redirect()->back();
            
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
        $subjectEdit = Subject::where('subject_id',$subject_id)->first();
        $teachers = Teacher::all();
        $classes = Classe::all();
        return view('subjects.subject_edit',compact('subjectEdit', 'teachers', 'classes'));
    }

    /** update record */
    public function updateRecord(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'subject_name' => 'required|string|max:255',
                'teacher_name' => 'required|string|max:255',
                'class' => 'required|string|max:255',
            ], [
                'subject_name.required' => 'Subject name is required',
                'teacher_name.required' => 'Teacher name is required',
                'class.required' => 'Class is required',
            ]);
            
            $updateRecord = [
                'subject_name' => $request->subject_name,
                'teacher_name' => $request->teacher_name,
                'class'        => $request->class,
            ];

            Subject::where('subject_id',$request->subject_id)->update($updateRecord);
            Toastr::success('Has been update successfully :)','Success');
            DB::commit();
            return redirect()->back();
           
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

            Subject::where('subject_id',$request->subject_id)->delete();
            DB::commit();
            Toastr::success('Deleted record successfully :)','Success');
            return redirect()->back();
        } catch(\Exception $e) {
            DB::rollback();
            Toastr::error('Deleted record fail :)','Error');
            return redirect()->back();
        }
    }

}
