<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Hash;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Teacher;
use Brian2694\Toastr\Facades\Toastr;

class TeacherController extends Controller
{
    /** add teacher page */
    public function teacherAdd()
    {
        $users = User::where('role_name','Teachers')->get();
        return view('teacher.add-teacher',compact('users'));
    }

    /** teacher list */
    public function teacherList()
{
    $listTeacher = Teacher::all();
    return view('teacher.list-teachers', compact('listTeacher'));
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
        ]);

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

            // optional: generate a teacher_id automatically if you need it
            $teacher->user_id = 'T' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

            $teacher->save();

            Toastr::success('Teacher has been added successfully :)', 'Success');
            return redirect()->route('teacher/list/page');
        } catch (\Exception $e) {
            \Log::error($e);
            Toastr::error('Failed to add teacher :)', 'Error');
            return redirect()->back();
        }
    }


    /** edit record */
    public function editRecord($id)
{
    $teacher = Teacher::find($id);

    if (!$teacher) {
        abort(404, 'Teacher not found');
    }

    return view('teacher.edit-teacher', compact('teacher'));
}


    /** update record teacher */
    public function updateRecordTeacher(Request $request)
    {
        DB::beginTransaction();
        try {

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
            ];
            Teacher::where('id',$request->id)->update($updateRecord);
            
            Toastr::success('Has been update successfully :)','Success');
            DB::commit();
            return redirect()->back();
           
        } catch(\Exception $e) {
            DB::rollback();
            \Log::info($e);
            Toastr::error('fail, update record  :)','Error');
            return redirect()->back();
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

}
