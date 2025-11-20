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
    public function teacherList()
{
    $listTeacher = Teacher::orderBy('id', 'desc')->paginate(10);
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
            'is_class_teacher' => 'nullable|in:yes,no',
            'class_teacher_id' => 'required_if:is_class_teacher,yes|nullable|exists:classes,id',
            'subject_class' => 'nullable|array',
            'subject_class.*.subject_id' => 'required_with:subject_class|exists:subjects,id',
            'subject_class.*.class_id' => 'required_with:subject_class|exists:classes,id',
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
            
            // Set class teacher if provided
            if ($request->is_class_teacher == 'yes' && $request->class_teacher_id) {
                $teacher->class_teacher_id = $request->class_teacher_id;
            }

            // optional: generate a teacher_id automatically if you need it
            $teacher->user_id = 'T' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

            $teacher->save();

            // Save subject-class assignments
            if ($request->has('subject_class') && is_array($request->subject_class)) {
                foreach ($request->subject_class as $assignment) {
                    if (!empty($assignment['subject_id']) && !empty($assignment['class_id'])) {
                        $teacher->subjectClasses()->attach($assignment['class_id'], [
                            'subject_id' => $assignment['subject_id']
                        ]);
                    }
                }
            }

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

}
