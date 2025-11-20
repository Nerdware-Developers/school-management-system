<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Student;
use App\Models\StudentFeeTerm;
use App\Models\FeesInformation;
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

            $feeAmount = (float) $request->input('fee_amount', 0);
            $amountPaid = (float) $request->input('amount_paid', 0);
            $student->balance = $feeAmount - $amountPaid;
            $student->save();

            $this->syncInitialFeeTerm($student, $request, $feeAmount, $amountPaid);

            DB::commit();
            Toastr::success('Student added successfully!', 'Success');
            return redirect()->back();
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

    /** student profile page */
    public function studentProfile($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            abort(404, 'Student not found');
        }
        
        $studentProfile = Student::with(['feeTerms' => function ($query) {
            $query->orderByDesc('created_at');
        }])->findOrFail($id);

        $payments = FeesInformation::with('term')
            ->where('student_id', $id)
            ->orderByDesc('paid_date')
            ->orderByDesc('id')
            ->get();

        $feeTerms = $studentProfile->feeTerms;
        $currentTerm = $feeTerms->firstWhere('status', 'current') ?? $feeTerms->first();
        $previousTerm = $feeTerms->first(function ($term) use ($currentTerm) {
            return $currentTerm && $term->id !== $currentTerm->id;
        });

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

        return view('student.student-profile', compact(
            'studentProfile',
            'feePerTerm',
            'amountPaid',
            'balance',
            'feeTerms',
            'currentTerm',
            'financialSummary',
            'payments'
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

    protected function syncInitialFeeTerm(Student $student, Request $request, float $feeAmount, float $amountPaid): void
    {
        if ($feeAmount == 0 && $amountPaid == 0) {
            return;
        }

        $closingBalance = $feeAmount - $amountPaid;

        $status = $closingBalance > 0 ? 'current' : ($closingBalance < 0 ? 'credit' : 'closed');

        $student->feeTerms()->create([
            'term_name' => $request->filled('term_name')
                ? $request->term_name
                : $this->generateTermName($student, $request->input('financial_year')),
            'academic_year' => $request->input('financial_year'),
            'fee_amount' => $feeAmount,
            'amount_paid' => $amountPaid,
            'opening_balance' => 0,
            'closing_balance' => $closingBalance,
            'status' => $status,
        ]);

        $student->balance = max($closingBalance, 0);
        $student->save();
    }

    protected function generateTermName(Student $student, ?string $academicYear = null): string
    {
        $count = $student->feeTerms()->count() + 1;
        $year = $academicYear ?: now()->format('Y');

        return "Term {$count} ({$year})";
    }
}
