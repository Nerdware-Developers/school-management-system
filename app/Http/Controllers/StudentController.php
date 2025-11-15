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
    public function student(Request $request)
{
    $query = Student::query();

    // Filter by class
    if ($request->filled('class')) {
        $query->where('class', 'LIKE', '%' . $request->class . '%');
    }

    // Filter by student name
    if ($request->filled('name')) {
        $search = trim($request->name);
        $nameParts = explode(' ', $search);

        // If two words are given (first + last)
        if (count($nameParts) >= 2) {
            $query->where(function ($q) use ($nameParts) {
                $q->where('first_name', 'LIKE', "%{$nameParts[0]}%")
                  ->where('last_name', 'LIKE', "%{$nameParts[1]}%");
            });
        } else {
            // If only one word, search in both first and last names
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%");
            });
        }
    }

    // Order and get results
    $studentList = $query->orderBy('class')->get();

    return view('student.student', compact('studentList'));
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
        return view('student.create');
    }
    
    /** student save record */
    public function studentSave(Request $request)
    {
        $request->validate([
            // 🧾 Legal Section
            'first_name'        => 'required|string|max:255',
            'last_name'         => 'required|string|max:255',
            'gender'            => 'required|string',
            'date_of_birth'     => 'required|date',
            'roll'              => 'nullable|string|max:100',
            'class'             => 'required|string|max:100',
            'admission_number'  => 'required|string|unique:students,admission_number',
            'address'           => 'nullable|string|max:255',
            'image'             => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,bmp,tiff,svg|max:5120',

            // 👨‍👩‍👧 Parent Information
            'parent_name'       => 'nullable|string|max:255',
            'parent_number'     => 'nullable|string|max:20',
            'parent_relationship' => 'nullable|string|max:100',
            'parent_email'      => 'nullable|email|max:255',
            'guardian_name'     => 'nullable|string|max:255',
            'guardian_number'   => 'nullable|string|max:20',
            'guardian_email'    => 'nullable|email|max:255',

            // ⚽ Co-Activities
            'sports'            => 'nullable|string|max:255',
            'clubs'             => 'nullable|string|max:255',
        

            // 🏥 Medical Information
            'blood_group'       => 'nullable|string|max:10',
            'known_allergies'   => 'nullable|string|max:255',
            'medical_condition' => 'nullable|string|max:255',
            'doctor_contact'    => 'nullable|string|max:20',
            'emergency_contact' => 'nullable|string|max:20',

            // 💰 Financial Information
            'fee_amount'        => 'nullable|numeric',
            'financial_year'    => 'nullable|string|max:50',
            'amount_paid'       => 'nullable|numeric',
            'fee_type'          => 'nullable|string|max:100',
            'payment_status'    => 'nullable|string|max:100',
            'transaction_id'    => 'nullable|string|max:255',
            'next_due_date'     => 'nullable|date',
            'scholarship'       => 'nullable|string|max:255',
            'sponsor_name'      => 'nullable|string|max:255',
        ]);
        // ✅ Fix date format (convert DD-MM-YYYY → YYYY-MM-DD)
        if ($request->filled('date_of_birth')) {
            try {
                $formattedDate = \Carbon\Carbon::createFromFormat('d-m-Y', $request->date_of_birth)->format('Y-m-d');
                $request->merge(['date_of_birth' => $formattedDate]);
            } catch (\Exception $e) {
                // ignore if already correct
            }
        }
        // Convert date format if needed
        if ($request->filled('next_due_date')) {
            try {
                $formattedDate = \Carbon\Carbon::createFromFormat('d-m-Y', $request->next_due_date)->format('Y-m-d');
                $request->merge(['next_due_date' => $formattedDate]);
            } catch (\Exception $e) {
                // If it's already in Y-m-d, ignore
            }
        }


        DB::beginTransaction();

try {
    $student = new Student;

    // Fill all non-file fields first
    $student->fill($request->except(['image', 'balance']));

    // Handle Image Upload
    if ($request->hasFile('image')) {
        $file = $request->file('image');

        // Generate a clean unique name
        $fileName = time() . '_' . $file->getClientOriginalName();

        // Store the file inside storage/app/public/student-photos
        $file->storeAs('public/student-photos', $fileName);

        // Save only the filename to the DB
        $student->image = $fileName;
    }

    // Auto-calculate balance
    $feeAmount = $request->input('fee_amount', 0);
    $amountPaid = $request->input('amount_paid', 0);
    $student->balance = $feeAmount - $amountPaid;

    $student->save();

    DB::commit();
    Toastr::success('Student added successfully!', 'Success');
    return redirect()->back();

} catch (\Exception $e) {
    DB::rollback();
    dd($e->getMessage());
    Toastr::error('Failed to add student!', 'Error');
    return redirect()->back()->withErrors(['error' => $e->getMessage()]);
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
        $student = Student::findOrFail($request->id);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if (!empty($student->image) && file_exists(storage_path('app/public/student-photos/'.$student->image))) {
                unlink(storage_path('app/public/student-photos/'.$student->image));
            }

            // Save new image
            $fileName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->storeAs('public/student-photos', $fileName);
        } else {
            $fileName = $request->image_hidden; // keep the old image
        }

        // Update all fields
        $student->update([
            'first_name'        => $request->first_name,
            'last_name'         => $request->last_name,
            'gender'            => $request->gender,
            'date_of_birth'     => $request->date_of_birth,
            'roll'              => $request->roll,
            'blood_group'       => $request->blood_group,
            'parent_email'      => $request->parent_email,
            'class'             => $request->class,
            'admission_number'  => $request->admission_number,
            'parent_name'       => $request->parent_name,
            'parent_number'     => $request->parent_number,
            'guardian_name'     => $request->guardian_name,
            'guardian_number'   => $request->guardian_number,
            'address'           => $request->address,
            'image'             => $fileName, // ✅ correct column name
        ]);

        DB::commit();
        Toastr::success('Student has been updated successfully!', 'Success');
        return redirect()->back();

    } catch (\Exception $e) {
        DB::rollback();
        Toastr::error('Failed to update student: '.$e->getMessage(), 'Error');
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
        $studentProfile = Student::findOrFail($id);

        // These columns already exist in your students table
        $feePerTerm = $studentProfile->fee_amount ?? 0;
        $amountPaid = $studentProfile->amount_paid ?? 0;
        $balance = $studentProfile->balance ?? ($feePerTerm - $amountPaid);

        return view('student.student-profile', compact(
            'studentProfile',
            'feePerTerm',
            'amountPaid',
            'balance'
        ));
    }

    public function create()
    {
        return view('students.create');
    }
    public function activities()
{
    return view('student.partials.activities');
}




}
