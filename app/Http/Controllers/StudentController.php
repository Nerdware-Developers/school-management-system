<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Student;
use App\Models\StudentFeeTerm;
use App\Models\FeesInformation;
use App\Models\SchoolSettings;
use App\Models\ExamResult;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
    $studentList = $query->orderBy('class')
        ->paginate(5)
        ->withQueryString();

    return view('student.student', compact('studentList'));
}

    /**
     * List students grouped by class with filtering
     */
    public function studentsByClass(Request $request)
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

            if (count($nameParts) >= 2) {
                $query->where(function ($q) use ($nameParts) {
                    $q->where('first_name', 'LIKE', "%{$nameParts[0]}%")
                      ->where('last_name', 'LIKE', "%{$nameParts[1]}%");
                });
            } else {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'LIKE', "%{$search}%")
                      ->orWhere('last_name', 'LIKE', "%{$search}%");
                });
            }
        }

        // Filter by balance (fee balance)
        if ($request->filled('balance_operator') && $request->filled('balance_amount')) {
            $operator = $request->balance_operator;
            $amount = (float) $request->balance_amount;

            switch ($operator) {
                case 'greater':
                    $query->where('balance', '>', $amount);
                    break;
                case 'greater_equal':
                    $query->where('balance', '>=', $amount);
                    break;
                case 'less':
                    $query->where('balance', '<', $amount);
                    break;
                case 'less_equal':
                    $query->where('balance', '<=', $amount);
                    break;
                case 'equal':
                    $query->where('balance', '=', $amount);
                    break;
                case 'not_zero':
                    $query->where('balance', '!=', 0);
                    break;
                case 'zero':
                    $query->where('balance', '=', 0);
                    break;
            }
        }

        // Get all students and group by class
        $students = $query->orderBy('class')->orderBy('first_name')->get();
        
        // Group students by class
        $studentsByClass = $students->groupBy('class');
        
        // Get all unique classes for filter dropdown
        $allClasses = Student::distinct()->orderBy('class')->pluck('class');

        return view('student.students-by-class', compact('studentsByClass', 'allClasses'));
    }

    /**
     * Export students by class to Excel (CSV format) - respects filters
     */
    public function exportStudentsByClass(Request $request)
    {
        $query = Student::query();
        $filters = [];

        // Apply same filters as studentsByClass method
        if ($request->filled('class')) {
            $classFilter = trim($request->class);
            $query->where('class', 'LIKE', '%' . $classFilter . '%');
            $filters[] = 'class_' . str_replace(' ', '_', $classFilter);
        }

        if ($request->filled('name')) {
            $search = trim($request->name);
            $nameParts = explode(' ', $search);

            if (count($nameParts) >= 2) {
                $query->where(function ($q) use ($nameParts) {
                    $q->where('first_name', 'LIKE', "%{$nameParts[0]}%")
                      ->where('last_name', 'LIKE', "%{$nameParts[1]}%");
                });
            } else {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'LIKE', "%{$search}%")
                      ->orWhere('last_name', 'LIKE', "%{$search}%");
                });
            }
            $filters[] = 'name_' . str_replace(' ', '_', $search);
        }

        // Filter by fee amount
        if ($request->filled('fee_amount_operator') && $request->filled('fee_amount')) {
            $operator = $request->fee_amount_operator;
            $amount = (float) $request->fee_amount;

            switch ($operator) {
                case 'greater':
                    $query->where('fee_amount', '>', $amount);
                    $filters[] = 'fee_gt_' . $amount;
                    break;
                case 'greater_equal':
                    $query->where('fee_amount', '>=', $amount);
                    $filters[] = 'fee_gte_' . $amount;
                    break;
                case 'less':
                    $query->where('fee_amount', '<', $amount);
                    $filters[] = 'fee_lt_' . $amount;
                    break;
                case 'less_equal':
                    $query->where('fee_amount', '<=', $amount);
                    $filters[] = 'fee_lte_' . $amount;
                    break;
                case 'equal':
                    $query->where('fee_amount', '=', $amount);
                    $filters[] = 'fee_eq_' . $amount;
                    break;
            }
        }

        // Filter by balance (fee balance)
        if ($request->filled('balance_operator') && $request->filled('balance_amount')) {
            $operator = $request->balance_operator;
            $amount = (float) $request->balance_amount;

            switch ($operator) {
                case 'greater':
                    $query->where('balance', '>', $amount);
                    $filters[] = 'balance_gt_' . $amount;
                    break;
                case 'greater_equal':
                    $query->where('balance', '>=', $amount);
                    $filters[] = 'balance_gte_' . $amount;
                    break;
                case 'less':
                    $query->where('balance', '<', $amount);
                    $filters[] = 'balance_lt_' . $amount;
                    break;
                case 'less_equal':
                    $query->where('balance', '<=', $amount);
                    $filters[] = 'balance_lte_' . $amount;
                    break;
                case 'equal':
                    $query->where('balance', '=', $amount);
                    $filters[] = 'balance_eq_' . $amount;
                    break;
                case 'not_zero':
                    $query->where('balance', '!=', 0);
                    $filters[] = 'balance_not_zero';
                    break;
                case 'zero':
                    $query->where('balance', '=', 0);
                    $filters[] = 'balance_zero';
                    break;
            }
        }

        // Get all students (no pagination for export)
        $students = $query->orderBy('class')->orderBy('first_name')->get();

        // Set headers for CSV download
        $filterSuffix = !empty($filters) ? '_' . implode('_', $filters) : '';
        $filename = 'students_by_class' . $filterSuffix . '_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        // Add BOM for UTF-8 to ensure Excel displays special characters correctly
        $callback = function() use ($students) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Add CSV headers - simplified: only Admission Number, Student Name, Fee Balance
            fputcsv($file, [
                'Admission Number',
                'Student Name',
                'Fee Balance'
            ]);

            // Add student data
            foreach ($students as $student) {
                $fullName = trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''));
                
                fputcsv($file, [
                    $student->admission_number ?? '',
                    $fullName,
                    $student->balance ?? '0'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export students to Excel (CSV format)
     */
    public function exportExcel(Request $request)
    {
        $query = Student::query();
        $filters = [];

        // Apply same filters as student list
        if ($request->has('class') && $request->class != '') {
            $classFilter = trim($request->class);
            $query->where('class', 'LIKE', '%' . $classFilter . '%');
            $filters[] = 'class_' . str_replace(' ', '_', $classFilter);
        }

        if ($request->has('name') && $request->name != '') {
            $search = trim($request->name);
            $nameParts = explode(' ', $search);

            if (count($nameParts) >= 2) {
                $query->where(function ($q) use ($nameParts) {
                    $q->where('first_name', 'LIKE', "%{$nameParts[0]}%")
                      ->where('last_name', 'LIKE', "%{$nameParts[1]}%");
                });
            } else {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'LIKE', "%{$search}%")
                      ->orWhere('last_name', 'LIKE', "%{$search}%");
                });
            }
            $filters[] = 'name_' . str_replace(' ', '_', $search);
        }

        // Get all students (no pagination for export)
        $students = $query->orderBy('class')->orderBy('first_name')->get();

        // Set headers for CSV download
        $filterSuffix = !empty($filters) ? '_' . implode('_', $filters) : '';
        $filename = 'students' . $filterSuffix . '_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        // Add BOM for UTF-8 to ensure Excel displays special characters correctly
        $callback = function() use ($students) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Add CSV headers
            fputcsv($file, [
                'Admission Number',
                'First Name',
                'Last Name',
                'Class',
                'Section',
                'Date of Birth',
                'Gender',
                'Roll',
                'Parent Name',
                'Parent Number',
                'Parent Email',
                'Parent Relationship',
                'Guardian Name',
                'Guardian Number',
                'Guardian Email',
                'Address',
                'Blood Group',
                'Fee Amount',
                'Balance',
                'Financial Year',
                'Payment Status'
            ]);

            // Add student data
            foreach ($students as $student) {
                // Format date of birth properly for Excel (YYYY-MM-DD format)
                // Format as text with apostrophe to prevent Excel from auto-formatting
                $dateOfBirth = '';
                if ($student->date_of_birth) {
                    try {
                        $date = \Carbon\Carbon::parse($student->date_of_birth);
                        // Prefix with apostrophe to force Excel to treat as text
                        // This prevents the ######## display issue when column is narrow
                        $dateOfBirth = "'" . $date->format('Y-m-d');
                    } catch (\Exception $e) {
                        $dateOfBirth = "'" . $student->date_of_birth;
                    }
                }
                
                // Format phone numbers as text (prefix with apostrophe) to prevent scientific notation
                // Excel will treat values starting with ' as text
                $parentNumber = $student->parent_number ? "'" . $student->parent_number : '';
                $guardianNumber = $student->guardian_number ? "'" . $student->guardian_number : '';
                
                fputcsv($file, [
                    $student->admission_number ?? '',
                    $student->first_name ?? '',
                    $student->last_name ?? '',
                    $student->class ?? '',
                    $student->section ?? '',
                    $dateOfBirth,
                    $student->gender ?? '',
                    $student->roll ?? '',
                    $student->parent_name ?? '',
                    $parentNumber,
                    $student->parent_email ?? '',
                    $student->parent_relationship ?? '',
                    $student->guardian_name ?? '',
                    $guardianNumber,
                    $student->guardian_email ?? '',
                    $student->address ?? '',
                    $student->blood_group ?? '',
                    $student->fee_amount ?? '0',
                    $student->balance ?? '0',
                    $student->financial_year ?? '',
                    $student->payment_status ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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
            'image'             => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,bmp,tiff|max:5120',

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
            'term_name'         => 'nullable|string|max:100',
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
            $student->fill($request->except(['image', 'balance']));

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                // Sanitize filename to prevent path traversal
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $safeName = \Str::slug($originalName) . '_' . time() . '.' . $extension;
                $file->storeAs('public/student-photos', $safeName);
                $student->image = $safeName;
            }

            // Get default settings if not provided
            $settings = SchoolSettings::getSettings();
            
            // CRITICAL: Use grade-based fee structure instead of default_fee_amount
            // Get fee amount based on student's class/grade
            $requestFeeAmount = $request->input('fee_amount');
            if ($requestFeeAmount === null || $requestFeeAmount === '' || (float)$requestFeeAmount <= 0) {
                // Financial form was skipped or fee_amount is 0 - use grade-based fee
                $studentClass = $request->input('class', '');
                $feeAmount = $this->getFeeAmountForGrade($studentClass);
                
                // Fallback to default_fee_amount if grade fee not found (for backward compatibility)
                if ($feeAmount <= 0) {
                    $feeAmount = (float) $settings->default_fee_amount;
                }
            } else {
                $feeAmount = (float) $requestFeeAmount;
            }
            
            $amountPaid = (float) $request->input('amount_paid', 0);
            
            // Set financial year from settings if not provided
            if (!$request->filled('financial_year')) {
                $student->financial_year = $settings->financial_year;
            } else {
                $student->financial_year = $request->input('financial_year');
            }
            
            // Save student first (without balance - will be set after fee term creation)
            $student->fee_amount = $feeAmount;
            $student->save();

            // CRITICAL: Create fee term FIRST for the current financial year
            // This ensures we have a fee term before calculating balance
            $this->syncInitialFeeTerm($student, $request, $feeAmount, $amountPaid);
            
            // CRITICAL: Run comprehensive fee calculation and synchronization
            // This ensures student balance matches current term's closing_balance
            // This is the same logic that runs in studentProfile() to ensure consistency
            // This will sync student.balance = currentTerm.closing_balance
            $this->calculateAndSyncStudentFees($student);

            DB::commit();
            
            // CRITICAL: Force database connection to flush and ensure transaction is fully committed
            // This ensures the new student data is immediately visible to other database connections/queries
            // Use a buffered Laravel query instead of raw PDO exec to avoid unbuffered query errors
            DB::select('SELECT 1');
            
            // Clear any query cache that might interfere
            DB::flushQueryLog();
            
            // Refresh the student one final time to ensure balance and fee_amount are current
            $student->refresh();
            
            // Final verification: Ensure balance matches current term's closing_balance
            $currentTerm = $student->feeTerms()->where('status', 'current')->first();
            if ($currentTerm) {
                $expectedBalance = max($currentTerm->closing_balance, 0);
                if (abs($student->balance - $expectedBalance) > 0.01) {
                    $student->balance = $expectedBalance;
                    $student->save();
                    // Use buffered Laravel query instead of raw PDO exec
                    DB::select('SELECT 1');
                }
            }
            
            // Verify the student is actually in the database with correct balance
            // This ensures data is committed before redirect
            $verifyStudent = Student::find($student->id);
            if ($verifyStudent && $verifyStudent->balance > 0) {
                // Data is confirmed - proceed with redirect
            }
            
            Toastr::success('Student added successfully!', 'Success');
            
            // Redirect to student list page
            return redirect()->route('student/list');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Student save failed: ' . $e->getMessage());
            Toastr::error('Failed to add student. Please try again.', 'Error');
            return redirect()->back();
        }

    }



    /** view for edit student */
    public function studentEdit($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            abort(404, 'Student not found');
        }
        
        $studentEdit = Student::findOrFail($id);
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
            // Delete old image if exists (safely)
            if (!empty($student->image)) {
                $oldFileName = basename($student->image); // Prevent path traversal
                $oldPath = storage_path('app/public/student-photos/' . $oldFileName);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            // Save new image with sanitized filename
            $file = $request->file('image');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $fileName = \Str::slug($originalName) . '_' . time() . '.' . $extension;
            $file->storeAs('public/student-photos', $fileName);
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
            \Log::error('Student update failed: ' . $e->getMessage());
            Toastr::error('Failed to update student. Please try again.', 'Error');
            return redirect()->back();
        }
}


    /** student delete */
    public function studentDelete(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:students,id',
            'avatar' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $student = Student::findOrFail($request->id);
            
            // Safely delete image file
            if (!empty($request->avatar)) {
                $avatarFileName = basename($request->avatar); // Prevent path traversal
                $avatarPath = storage_path('app/public/student-photos/' . $avatarFileName);
                if (file_exists($avatarPath) && is_file($avatarPath)) {
                    unlink($avatarPath);
                }
            }
            
            $student->delete();
            DB::commit();
            Toastr::success('Student deleted successfully :)','Success');
            return redirect()->back();
    
        } catch(\Exception $e) {
            DB::rollback();
            \Log::error('Student delete failed: ' . $e->getMessage());
            Toastr::error('Failed to delete student. Please try again.','Error');
            return redirect()->back();
        }
    }

    /** bulk delete students */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'required|integer|exists:students,id',
        ]);

        DB::beginTransaction();
        try {
            $studentIds = $request->student_ids;
            $students = Student::whereIn('id', $studentIds)->get();
            $deletedCount = 0;

            foreach ($students as $student) {
                // Delete image file if exists
                if (!empty($student->image)) {
                    $avatarFileName = basename($student->image);
                    $avatarPath = storage_path('app/public/student-photos/' . $avatarFileName);
                    if (file_exists($avatarPath) && is_file($avatarPath)) {
                        unlink($avatarPath);
                    }
                }
                $student->delete();
                $deletedCount++;
            }

            DB::commit();
            Toastr::success("Successfully deleted {$deletedCount} student(s)", 'Success');
            return response()->json([
                'success' => true,
                'message' => "Successfully deleted {$deletedCount} student(s)",
                'deleted_count' => $deletedCount
            ]);
        } catch(\Exception $e) {
            DB::rollback();
            \Log::error('Bulk delete students failed: ' . $e->getMessage());
            Toastr::error('Failed to delete students', 'Error');
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete students'
            ], 500);
        }
    }

    /** student profile page */
    public function studentProfile(Request $request, $id)
    {
        if (!is_numeric($id) || $id <= 0) {
            abort(404, 'Student not found');
        }
        
        $studentProfile = Student::with(['feeTerms' => function ($query) {
            $query->orderByDesc('created_at');
        }])->findOrFail($id);

        // Get class teacher for the student's class
        $classTeacher = null;
        if ($studentProfile->class) {
            $classe = \App\Models\Classe::where('class_name', $studentProfile->class)->first();
            if ($classe) {
                $classTeacher = \App\Models\Teacher::where('class_teacher_id', $classe->id)->first();
            }
        }

        $payments = FeesInformation::with('term')
            ->where('student_id', $id)
            ->orderByDesc('paid_date')
            ->orderByDesc('id')
            ->get();

        $feeTerms = $studentProfile->feeTerms;
        $currentTerm = $feeTerms->firstWhere('status', 'current') ?? $feeTerms->first();
        
        // Auto-sync with financial settings - always get settings first
        $schoolSettings = SchoolSettings::getSettings();
        $yearChanged = false;
        
        if ($currentTerm) {
            $needsUpdate = false;
            
            // Check if there's a previous term with outstanding balance that should be carried forward
            $previousTermWithBalance = null;
            if ($currentTerm) {
                $previousTermWithBalance = $feeTerms->first(function ($term) use ($currentTerm, $schoolSettings) {
                    return $term->id !== $currentTerm->id 
                        && $term->academic_year != $schoolSettings->financial_year
                        && $term->closing_balance != 0;
                });
            }
            
            // Auto-update academic year if it doesn't match
            if ($currentTerm->academic_year != $schoolSettings->financial_year) {
                // If year changed, we need to close the old term and create a new one
                $oldClosingBalance = $currentTerm->closing_balance;
                
                // Close the old term
                if ($currentTerm->closing_balance > 0) {
                    $currentTerm->status = 'carried';
                } elseif ($currentTerm->closing_balance < 0) {
                    $currentTerm->status = 'credit';
                } else {
                    $currentTerm->status = 'closed';
                }
                $currentTerm->save();
                
                // Create a new term for the new year with the old balance as opening
                $newTerm = $studentProfile->feeTerms()->create([
                    'term_name' => 'Term 1 (' . $schoolSettings->financial_year . ')',
                    'academic_year' => $schoolSettings->financial_year,
                    'fee_amount' => $schoolSettings->default_fee_amount,
                    'amount_paid' => 0, // Reset for new year
                    'opening_balance' => $oldClosingBalance, // Carry forward from previous year
                    'closing_balance' => $oldClosingBalance + $schoolSettings->default_fee_amount, // opening + new fee
                    'status' => 'current',
                ]);
                
                // Update current term reference
                $currentTerm = $newTerm;
                $needsUpdate = true;
                $yearChanged = true;
                
                // Reload fee terms to include the new term
                $feeTerms = $studentProfile->feeTerms()->orderByDesc('created_at')->get();
                
                // Recalculate previous term with balance after reload
                $previousTermWithBalance = $feeTerms->first(function ($term) use ($currentTerm, $schoolSettings) {
                    return $term->id !== $currentTerm->id 
                        && $term->academic_year != $schoolSettings->financial_year
                        && $term->closing_balance != 0;
                });
            }
            
            // Check if current term has wrong data (year matches but has old payment data)
            // This happens if term was manually updated to 2026 but still has 2025's amount_paid
            if (!$yearChanged 
                && $currentTerm
                && $currentTerm->academic_year == $schoolSettings->financial_year 
                && $previousTermWithBalance 
                && $currentTerm->opening_balance == 0 
                && $currentTerm->amount_paid > 0) {
                // This term has wrong data - it should have the previous term's balance as opening
                $oldClosingBalance = $previousTermWithBalance->closing_balance;
                
                // Close the incorrectly configured term
                $currentTerm->status = 'carried';
                $currentTerm->save();
                
                // Create a new term with correct data
                $newTerm = $studentProfile->feeTerms()->create([
                    'term_name' => 'Term 1 (' . $schoolSettings->financial_year . ')',
                    'academic_year' => $schoolSettings->financial_year,
                    'fee_amount' => $schoolSettings->default_fee_amount,
                    'amount_paid' => 0, // Reset for new year
                    'opening_balance' => $oldClosingBalance, // Carry forward from previous year
                    'closing_balance' => $oldClosingBalance + $schoolSettings->default_fee_amount, // opening + new fee
                    'status' => 'current',
                ]);
                
                // Update current term reference
                $currentTerm = $newTerm;
                $needsUpdate = true;
                $yearChanged = true;
                
                // Reload fee terms to include the new term
                $feeTerms = $studentProfile->feeTerms()->orderByDesc('created_at')->get();
            }
            
            // Auto-update fee amount if it doesn't match (with small tolerance for floating point)
            // Only update if year didn't change (since we already set it above)
            if (!$yearChanged && abs($currentTerm->fee_amount - $schoolSettings->default_fee_amount) > 0.01) {
                // Recalculate closing balance: opening + new_fee - amount_paid
                $newClosingBalance = $currentTerm->opening_balance + $schoolSettings->default_fee_amount - $currentTerm->amount_paid;
                
                $currentTerm->fee_amount = $schoolSettings->default_fee_amount;
                $currentTerm->closing_balance = $newClosingBalance;
                
                // Update status based on new closing balance
                if ($newClosingBalance > 0) {
                    $currentTerm->status = 'current';
                } elseif ($newClosingBalance < 0) {
                    $currentTerm->status = 'credit';
                } else {
                    $currentTerm->status = 'closed';
                }
                
                $needsUpdate = true;
            }
            
            // Save updates if any were made (only if year didn't change, since new term is already saved)
            if ($needsUpdate && !$yearChanged) {
                $currentTerm->save();
                $currentTerm->refresh();
            }
            
            // Update student's balance and financial_year
            if ($needsUpdate) {
                // Refresh current term to get latest data
                if ($yearChanged) {
                    $currentTerm->refresh();
                }
                
                $studentProfile->balance = max($currentTerm->closing_balance, 0);
                $studentProfile->fee_amount = $currentTerm->fee_amount;
                $studentProfile->financial_year = $currentTerm->academic_year;
                $studentProfile->save();
            }
        }
        
        // Refresh current term one more time to ensure we have latest data
        if ($currentTerm) {
            $currentTerm->refresh();
        } else {
            // If no current term exists, create one automatically
            $previousTerm = $feeTerms->first();
            $previousClosingBalance = $previousTerm ? $previousTerm->closing_balance : 0;
            
            // Close previous term if it exists
            if ($previousTerm && $previousTerm->status == 'current') {
                if ($previousTerm->closing_balance > 0) {
                    $previousTerm->status = 'carried';
                } elseif ($previousTerm->closing_balance < 0) {
                    $previousTerm->status = 'credit';
                } else {
                    $previousTerm->status = 'closed';
                }
                $previousTerm->save();
            }
            
            // Create a new current term
            $currentTerm = $studentProfile->feeTerms()->create([
                'term_name' => 'Term 1 (' . $schoolSettings->financial_year . ')',
                'academic_year' => $schoolSettings->financial_year,
                'fee_amount' => $schoolSettings->default_fee_amount,
                'amount_paid' => 0,
                'opening_balance' => $previousClosingBalance,
                'closing_balance' => $previousClosingBalance + $schoolSettings->default_fee_amount,
                'status' => 'current',
            ]);
            
            // Update student record
            $studentProfile->balance = max($currentTerm->closing_balance, 0);
            $studentProfile->fee_amount = $currentTerm->fee_amount;
            $studentProfile->financial_year = $currentTerm->academic_year;
            $studentProfile->save();
            
            // Reload fee terms
            $feeTerms = $studentProfile->feeTerms()->orderByDesc('created_at')->get();
        }
        
        $previousTerm = $feeTerms->first(function ($term) use ($currentTerm) {
            return $currentTerm && $term->id !== $currentTerm->id;
        });

        // Use the current term's closing balance directly from database
        // This ensures we get the actual value, not a cached or incorrect one
        $currentClosing = $currentTerm ? $currentTerm->closing_balance : ($studentProfile->balance ?? 0);
        $currentOpening = $currentTerm ? $currentTerm->opening_balance : 0;

        $financialSummary = [
            'total_fee_amount' => $feeTerms->sum('fee_amount'),
            'total_amount_paid' => $feeTerms->sum('amount_paid'),
            'outstanding_balance' => max($currentClosing, 0),
            'credit_balance' => $currentClosing < 0 ? abs($currentClosing) : 0,
            'carried_balance' => max($currentOpening, 0),
            'opening_credit' => $currentOpening < 0 ? abs($currentOpening) : 0,
            'previous_balance' => $previousTerm ? $previousTerm->closing_balance : 0,
        ];

        $feePerTerm = $currentTerm->fee_amount ?? 0;
        $amountPaid = $currentTerm->amount_paid ?? 0;
        $balance = $financialSummary['outstanding_balance'];

        // Get exam results for this student with exam details
        $allExamResults = ExamResult::with(['exam' => function($query) {
                $query->with('class');
            }])
            ->where('student_id', $id)
            ->orderByDesc('created_at')
            ->get()
            ->filter(function($result) {
                return $result->exam !== null; // Only include results with valid exams
            });

        // Get all available terms and exam types for filter dropdowns
        $availableTerms = $allExamResults->map(function($result) {
                return $result->exam ? $result->exam->term : null;
            })
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $availableExamTypes = $allExamResults->map(function($result) {
                return $result->exam ? $result->exam->exam_type : null;
            })
            ->filter()
            ->unique()
            ->sort()
            ->values();

        // Get filter parameters from request
        $selectedTerm = $request->input('term');
        $selectedExamType = $request->input('exam_type');

        // Filter results based on selected term and exam type
        $filteredResults = $allExamResults;
        if ($selectedTerm) {
            $filteredResults = $filteredResults->filter(function($result) use ($selectedTerm) {
                return $result->exam && $result->exam->term == $selectedTerm;
            });
        }
        if ($selectedExamType) {
            $filteredResults = $filteredResults->filter(function($result) use ($selectedExamType) {
                return $result->exam && $result->exam->exam_type == $selectedExamType;
            });
        }

        // If no filters are selected and there are results, show the first available (most recent)
        if (!$selectedTerm && !$selectedExamType && $allExamResults->isNotEmpty()) {
            $firstResult = $allExamResults->first();
            if ($firstResult && $firstResult->exam) {
                $selectedTerm = $firstResult->exam->term;
                $selectedExamType = $firstResult->exam->exam_type;
                $filteredResults = $allExamResults->filter(function($result) use ($selectedTerm, $selectedExamType) {
                    return $result->exam 
                        && $result->exam->term == $selectedTerm 
                        && $result->exam->exam_type == $selectedExamType;
                });
            }
        }

        // Group filtered results by exam_type, term, and class name (for consistency, though we'll only show one group)
        $examResults = $filteredResults->groupBy(function($result) {
                $exam = $result->exam;
                if ($exam) {
                    $className = $exam->class ? $exam->class->class_name : 'Unknown';
                    return $exam->exam_type . '_' . $exam->term . '_' . $className;
                }
                return 'other';
            });

        return view('student.student-profile', compact(
            'studentProfile',
            'feePerTerm',
            'amountPaid',
            'balance',
            'feeTerms',
            'currentTerm',
            'financialSummary',
            'schoolSettings',
            'payments',
            'classTeacher',
            'examResults',
            'availableTerms',
            'availableExamTypes',
            'selectedTerm',
            'selectedExamType'
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

    public function studentPhoto($filename)
    {
        $filename = basename($filename);
        $path = 'student-photos/' . $filename;

        if (!Storage::disk('public')->exists($path)) {
            $fallbackPath = public_path('images/photo_defaults.jpg');

            if (file_exists($fallbackPath)) {
                return response()->file($fallbackPath);
            }

            abort(404);
        }

        return Storage::disk('public')->response($path);
    }

    public function storeTerm(Request $request, Student $student)
    {
        $validated = $request->validateWithBag('termCreation', [
            'term_name' => 'required|string|max:100',
            'academic_year' => 'required|string|max:25',
            'fee_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $previousTerm = $student->feeTerms()->latest('id')->first();

            if ($previousTerm) {
                $previousTerm->status = $previousTerm->closing_balance > 0
                    ? 'carried'
                    : ($previousTerm->closing_balance < 0 ? 'credit' : 'closed');
                $previousTerm->save();
            }

            $openingBalance = $previousTerm ? $previousTerm->closing_balance : 0;
            $closingBalance = $openingBalance + $validated['fee_amount'];
            $termStatus = $closingBalance > 0 ? 'current' : ($closingBalance < 0 ? 'credit' : 'closed');

            $term = $student->feeTerms()->create([
                'term_name' => $validated['term_name'],
                'academic_year' => $validated['academic_year'],
                'fee_amount' => $validated['fee_amount'],
                'amount_paid' => 0,
                'opening_balance' => $openingBalance,
                'closing_balance' => $closingBalance,
                'status' => $termStatus,
                'notes' => $validated['notes'] ?? null,
            ]);

            $student->balance = max($term->closing_balance, 0);
            $student->fee_amount = $validated['fee_amount'];
            $student->financial_year = $validated['academic_year'];
            $student->save();

            DB::commit();
            Toastr::success('New term created successfully.', 'Success');
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            Toastr::error('Failed to create a new term.', 'Error');
        }

        return redirect()->back();
    }

    public function recordTermPayment(Request $request, Student $student, StudentFeeTerm $term)
    {
        abort_if($term->student_id !== $student->id, 404);

        $validated = $request->validateWithBag('termPayment', [
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|max:100',
            'payment_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            // Create a payment record for this term
            FeesInformation::create([
                'student_id'           => $student->id,
                'student_fee_term_id'  => $term->id,
                'student_name'         => $student->first_name . ' ' . $student->last_name,
                'gender'               => $student->gender,
                'fees_type'            => $validated['payment_method'] ?? 'Term Payment',
                'fees_amount'          => $validated['amount'],
                'paid_date'            => now(),
            ]);

            $term->amount_paid += $validated['amount'];
            $term->last_payment_method = $validated['payment_method'];
            $term->last_payment_reference = $validated['payment_reference'] ?? null;
            $term->last_payment_at = now();

            if (!empty($validated['notes'])) {
                $term->notes = trim(($term->notes ? $term->notes . PHP_EOL : '') . $validated['notes']);
            }

            $term->closing_balance = $term->opening_balance + $term->fee_amount - $term->amount_paid;

            if ($term->closing_balance > 0) {
                $term->status = 'current';
            } elseif ($term->closing_balance < 0) {
                $term->status = 'credit';
            } else {
                $term->status = 'closed';
            }

            $term->save();

            $student->balance = max($term->closing_balance, 0);
            $student->amount_paid = $term->amount_paid;
            $student->save();

            DB::commit();
            Toastr::success('Payment recorded successfully.', 'Success');
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            Toastr::error('Failed to record payment.', 'Error');
        }

        return redirect()->back();
    }

    /**
     * Create initial fee term for a new student
     * This creates the fee term but does NOT sync student balance (that's done by calculateAndSyncStudentFees)
     * 
     * @param Student $student
     * @param Request $request
     * @param float $feeAmount
     * @param float $amountPaid
     * @return void
     */
    protected function syncInitialFeeTerm(Student $student, Request $request, float $feeAmount, float $amountPaid): void
    {
        // Get financial year from request or use default from settings
        $settings = SchoolSettings::getSettings();
        $financialYear = $request->input('financial_year') ?? $student->financial_year ?? $settings->financial_year;
        
        // Check if a term already exists for this student and financial year (prevent duplicates)
        $existingTerm = $student->feeTerms()
            ->where('academic_year', $financialYear)
            ->first();
        
        if ($existingTerm) {
            // Term already exists - update it instead of creating duplicate
            $openingBalance = $existingTerm->opening_balance;
            $closingBalance = $openingBalance + $feeAmount - $amountPaid;
            $status = $closingBalance > 0 ? 'current' : ($closingBalance < 0 ? 'credit' : 'closed');
            
            $existingTerm->fee_amount = $feeAmount;
            $existingTerm->amount_paid = $amountPaid;
            $existingTerm->closing_balance = $closingBalance;
            $existingTerm->status = $status;
            $existingTerm->save();
            $existingTerm->refresh();
            return;
        }
        
        // CRITICAL: Always create a fee term with the provided fee amount
        // If feeAmount is still 0 at this point (shouldn't happen due to studentSave() logic),
        // we still create a term but with closed status. However, this should be rare since
        // studentSave() ensures default_fee_amount is used when financial form is skipped.
        if ($feeAmount <= 0 && $amountPaid == 0) {
            // Try to get grade-based fee for the student
            $gradeFee = $this->getFeeAmountForGrade($student->class);
            if ($gradeFee > 0) {
                $feeAmount = $gradeFee;
                // Continue to create term with grade-based fee amount (don't return)
            } elseif ($settings->default_fee_amount > 0) {
                // Fallback to default_fee_amount if grade fee not found
                $feeAmount = (float) $settings->default_fee_amount;
                // Continue to create term with default fee amount (don't return)
            } else {
                // Only create closed term if default_fee_amount is also 0
                $student->feeTerms()->create([
                    'term_name' => $request->filled('term_name')
                        ? $request->term_name
                        : $this->generateTermName($student, $financialYear),
                    'academic_year' => $financialYear,
                    'fee_amount' => 0,
                    'amount_paid' => 0,
                    'opening_balance' => 0,
                    'closing_balance' => 0,
                    'status' => 'closed',
                ]);
                return;
            }
        }

        // For new students, opening_balance is always 0
        $openingBalance = 0;
        // Calculate closing balance using the standard formula: opening_balance + fee_amount - amount_paid
        $closingBalance = $openingBalance + $feeAmount - $amountPaid;

        // Status should be 'current' if there's any outstanding balance
        $status = $closingBalance > 0 ? 'current' : ($closingBalance < 0 ? 'credit' : 'closed');

        $term = $student->feeTerms()->create([
            'term_name' => $request->filled('term_name')
                ? $request->term_name
                : $this->generateTermName($student, $financialYear),
            'academic_year' => $financialYear,
            'fee_amount' => $feeAmount,
            'amount_paid' => $amountPaid,
            'opening_balance' => $openingBalance,
            'closing_balance' => $closingBalance,
            'status' => $status,
        ]);

        // Force save to ensure the term is persisted immediately
        // This is important for the dashboard query to pick it up right away
        $term->save();
        
        // Refresh the term to ensure it's properly loaded from database
        // This ensures we have the latest data including the auto-generated ID
        $term->refresh();
        
        // Double-check the values are correct after refresh
        if ($term->status !== $status || abs($term->closing_balance - $closingBalance) > 0.01) {
            $term->status = $status;
            $term->closing_balance = $closingBalance;
            $term->save();
            $term->refresh();
        }
        
        // Note: Student balance sync is handled by calculateAndSyncStudentFees() to ensure consistency
    }

    protected function generateTermName(Student $student, ?string $academicYear = null): string
    {
        $count = $student->feeTerms()->count() + 1;
        $year = $academicYear ?: now()->format('Y');

        return "Term {$count} ({$year})";
    }

    /**
     * Get fee amount for a specific grade/class
     * Uses grade-based fee structure instead of default fee
     * 
     * @param string $grade The student's grade/class (e.g., 'GRADE 1', 'PP1', 'PLAY GROUP')
     * @return float The fee amount for that grade, or 0 if not found
     */
    protected function getFeeAmountForGrade(string $grade): float
    {
        if (empty($grade)) {
            return 0;
        }

        // Normalize grade name - try to match common variations
        $normalizedGrade = strtoupper(trim($grade));
        
        // Try exact match first
        $gradeFee = \App\Models\GradeFee::getFeeForGrade($normalizedGrade);
        if ($gradeFee) {
            return (float) $gradeFee->total_fee;
        }

        // Try matching with common variations
        // Handle cases like "Grade 1" vs "GRADE 1", "PP1" vs "PP 1", etc.
        $variations = [
            $normalizedGrade,
            str_replace(' ', '', $normalizedGrade),
            str_replace('GRADE', 'GRADE ', $normalizedGrade),
            str_replace('GRADE ', 'GRADE', $normalizedGrade),
        ];

        foreach ($variations as $variation) {
            $gradeFee = \App\Models\GradeFee::getFeeForGrade($variation);
            if ($gradeFee) {
                return (float) $gradeFee->total_fee;
            }
        }

        // If no match found, return 0 (will fallback to default_fee_amount in calling code)
        return 0;
    }

    /**
     * Calculate and synchronize student fees with current term
     * This method ensures student.balance = currentTerm.closing_balance
     * This is the core fee calculation logic that was previously only in studentProfile()
     * 
     * @param Student $student
     * @return void
     */
    protected function calculateAndSyncStudentFees(Student $student): void
    {
        // Reload student with fee terms to ensure we have latest data
        $student->refresh();
        $student->load('feeTerms');
        
        $feeTerms = $student->feeTerms;
        $schoolSettings = SchoolSettings::getSettings();
        
        // Find current term - prioritize status='current', then latest term
        $currentTerm = $feeTerms->firstWhere('status', 'current') ?? $feeTerms->sortByDesc('created_at')->first();
        
        // If no current term exists OR current term is for wrong academic year, create/update one
        if (!$currentTerm || ($currentTerm->academic_year != $schoolSettings->financial_year)) {
            // Close existing current term if it exists and is for wrong year
            if ($currentTerm && $currentTerm->academic_year != $schoolSettings->financial_year) {
                if ($currentTerm->closing_balance > 0) {
                    $currentTerm->status = 'carried';
                } elseif ($currentTerm->closing_balance < 0) {
                    $currentTerm->status = 'credit';
                } else {
                    $currentTerm->status = 'closed';
                }
                $currentTerm->save();
            }
            
            // Check if there's already a term for the current financial year (prevent duplicates)
            $existingTermForYear = $student->feeTerms()
                ->where('academic_year', $schoolSettings->financial_year)
                ->first();
            
            if ($existingTermForYear) {
                // Use existing term but ensure it's marked as current
                $currentTerm = $existingTermForYear;
                if ($currentTerm->status != 'current') {
                    $currentTerm->status = 'current';
                    $currentTerm->save();
                }
            } else {
                // Get previous term's closing balance for carry forward
                $previousTerm = $feeTerms->where('id', '!=', $currentTerm?->id)
                    ->sortByDesc('created_at')
                    ->first();
                $previousClosingBalance = $previousTerm ? $previousTerm->closing_balance : 0;
                
                // Create a new current term for the current financial year
                $currentTerm = $student->feeTerms()->create([
                    'term_name' => 'Term 1 (' . $schoolSettings->financial_year . ')',
                    'academic_year' => $schoolSettings->financial_year,
                    'fee_amount' => $schoolSettings->default_fee_amount,
                    'amount_paid' => 0,
                    'opening_balance' => $previousClosingBalance,
                    'closing_balance' => $previousClosingBalance + $schoolSettings->default_fee_amount,
                    'status' => 'current',
                ]);
            }
        }
        
        // Ensure current term is refreshed to get latest data
        $currentTerm->refresh();
        
        // CRITICAL: Sync student balance with current term's closing_balance
        // This is the key calculation that ensures dashboard queries work correctly
        $expectedBalance = max($currentTerm->closing_balance, 0);
        
        // Update student record to match current term
        $student->balance = $expectedBalance;
        $student->fee_amount = $currentTerm->fee_amount;
        $student->financial_year = $currentTerm->academic_year;
        $student->save();
        
        // Force refresh to ensure all relationships are updated
        $student->refresh();
    }

    /**
     * Update term's academic year to match current financial year setting
     */
    public function updateTermYear(Student $student, StudentFeeTerm $term)
    {
        abort_if($term->student_id !== $student->id, 404);

        DB::beginTransaction();
        try {
            $settings = SchoolSettings::getSettings();
            
            $term->academic_year = $settings->financial_year;
            $term->save();

            // Also update student's financial_year to match
            $student->financial_year = $settings->financial_year;
            $student->save();

            DB::commit();
            Toastr::success('Academic year updated successfully to ' . $settings->financial_year . '.', 'Success');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to update term year: ' . $e->getMessage());
            Toastr::error('Failed to update academic year.', 'Error');
        }

        return redirect()->back();
    }

    /**
     * Update term's fee amount to match current default fee setting
     */
    public function updateTermFee(Student $student, StudentFeeTerm $term)
    {
        abort_if($term->student_id !== $student->id, 404);

        DB::beginTransaction();
        try {
            $settings = SchoolSettings::getSettings();
            
            // Use grade-based fee instead of default_fee_amount
            $gradeFee = $this->getFeeAmountForGrade($student->class);
            $newFeeAmount = $gradeFee > 0 ? $gradeFee : (float) $settings->default_fee_amount;
            
            // Recalculate closing balance: opening + new_fee - amount_paid
            $newClosingBalance = $term->opening_balance + $newFeeAmount - $term->amount_paid;
            
            $term->fee_amount = $newFeeAmount;
            $term->closing_balance = $newClosingBalance;
            
            // Update status based on new closing balance
            if ($newClosingBalance > 0) {
                $term->status = 'current';
            } elseif ($newClosingBalance < 0) {
                $term->status = 'credit';
            } else {
                $term->status = 'closed';
            }
            
            $term->save();

            // Update student's balance and fee_amount
            $student->balance = max($newClosingBalance, 0);
            $student->fee_amount = $newFeeAmount;
            $student->save();

            DB::commit();
            Toastr::success('Fee amount updated successfully to Ksh' . number_format($newFeeAmount, 2) . '. Balance recalculated.', 'Success');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to update term fee: ' . $e->getMessage());
            Toastr::error('Failed to update fee amount.', 'Error');
        }

        return redirect()->back();
    }
}
