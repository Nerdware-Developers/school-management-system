<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Student;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;

class StudentController extends Controller
{
    public function search(Request $request)
    {
        $term = $request->get('term');
        
        $students = \App\Models\Student::where('first_name', 'LIKE', "%$term%")
            ->orWhere('last_name', 'LIKE', "%$term%")
            ->get();

        $formatted = $students->map(function ($student) {
            return [
                'id' => $student->id,
                'text' => "{$student->first_name} {$student->last_name} (Class {$student->class})",
                'full_name' => "{$student->first_name} {$student->last_name}",
            ];
        });

        return response()->json($formatted);
    }


    /** index page student list */
    public function student()
    {
        $studentList = Student::all();
        return view('student.student',compact('studentList'));
    }

    /** index page student grid */
    public function studentGrid()
    {
        $studentList = Student::all();
        return view('student.student-grid',compact('studentList'));
    }

    /** student add page */
    public function studentAdd()
    {
        return view('student.add-student');
    }
    
    /** student save record */
    public function studentSave(Request $request)
    {
        $request->validate([
            'first_name'    => 'required|string',
            'last_name'     => 'required|string',
            'gender'        => 'required|not_in:0',
            'date_of_birth' => 'required|string',
            'roll'          => 'required|string',
            'blood_group'   => 'required|string',
            'religion'      => 'required|string',
            'email'         => 'required|email',
            'class'         => 'required|string',
            'section'       => 'required|string',
            'admission_id'  => 'required|string',
            'phone_number'  => 'required',
            'upload'        => 'required|image',
            'parent_name'   => 'required|string',
            'father_name' => 'nullable|string',
            'guardian_name' => 'nullable|string',
            'guardian_phone' => 'nullable|string',
            'address' => 'nullable|string|max:255',

            
        ]);
        
        DB::beginTransaction();
        try {
           
            $upload_file = rand() . '.' . $request->upload->extension();
            $request->upload->move(storage_path('app/public/student-photos/'), $upload_file);
            if(!empty($request->upload)) {
                $student = new Student;
                $student->first_name   = $request->first_name;
                $student->last_name    = $request->last_name;
                $student->gender       = $request->gender;
                $student->date_of_birth= $request->date_of_birth;
                $student->roll         = $request->roll;
                $student->blood_group  = $request->blood_group;
                $student->religion     = $request->religion;
                $student->email        = $request->email;
                $student->class        = $request->class;
                $student->section      = $request->section;
                $student->admission_id = $request->admission_id;
                $student->parent_name  = $request->parent_name;
                $student->phone_number = $request->phone_number;
                $student->father_name = $request->father_name;
                $student->guardian_name = $request->guardian_name;
                $student->address       = $request->address;
                $student->guardian_phone = $request->guardian_phone;

                $student->upload = $upload_file;
                $student->save();

                Toastr::success('Has been add successfully :)','Success');
                DB::commit();
            }

            return redirect()->back();
           
        } catch(\Exception $e) {
            DB::rollback();
            Toastr::error('fail, Add new student  :)','Error');
            return redirect()->back();
        }
    }

    /** view for edit student */
    public function studentEdit($id)
    {
        $studentEdit = Student::where('id',$id)->first();
        return view('student.edit-student',compact('studentEdit'));
    }

    /** update record */
    public function studentUpdate(Request $request)
    {
        DB::beginTransaction();
        try {
            // Handle file upload
            if (!empty($request->upload)) {
                // Delete old photo
                if (!empty($request->image_hidden) && file_exists(storage_path('app/public/student-photos/'.$request->image_hidden))) {
                    unlink(storage_path('app/public/student-photos/'.$request->image_hidden));
                }
                $upload_file = rand() . '.' . $request->upload->extension();
                $request->upload->move(storage_path('app/public/student-photos/'), $upload_file);
            } else {
                $upload_file = $request->image_hidden;
            }

            // Update all fields
            $updateRecord = [
                'first_name'     => $request->first_name,
                'last_name'      => $request->last_name,
                'gender'         => $request->gender,
                'date_of_birth'  => $request->date_of_birth,
                'roll'           => $request->roll,
                'blood_group'    => $request->blood_group,
                'religion'       => $request->religion,
                'email'          => $request->email,
                'class'          => $request->class,
                'section'        => $request->section,
                'admission_id'   => $request->admission_id,
                'parent_name'    => $request->parent_name,
                'phone_number'   => $request->phone_number,
                'father_name'    => $request->father_name,
                'guardian_name'  => $request->guardian_name,
                'guardian_phone' => $request->guardian_phone,
                'address'        => $request->address,
                'upload'         => $upload_file,
            ];

            Student::where('id', $request->id)->update($updateRecord);

            DB::commit();
            Toastr::success('Student has been updated successfully :)','Success');
            return redirect()->back();

        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Failed to update student :(','Error');
            return redirect()->back();
        }
    }


    /** student delete */
    public function studentDelete(Request $request)
    {
        DB::beginTransaction();
        try {
           
            if (!empty($request->id)) {
                Student::destroy($request->id);
                unlink(storage_path('app/public/student-photos/'.$request->avatar));
                DB::commit();
                Toastr::success('Student deleted successfully :)','Success');
                return redirect()->back();
            }
    
        } catch(\Exception $e) {
            DB::rollback();
            Toastr::error('Student deleted fail :)','Error');
            return redirect()->back();
        }
    }

    /** student profile page */
    public function studentProfile($id)
    {
        $studentProfile = Student::where('id',$id)->first();
        return view('student.student-profile',compact('studentProfile'));
    }
}
