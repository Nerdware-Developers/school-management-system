<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\FeesType;
use App\Models\FeesInformation;
use App\Models\Student; 
use Brian2694\Toastr\Facades\Toastr;

class AccountsController extends Controller
{
    /** Show all fee collections */
    public function index()
    {
        $feesInformation = FeesInformation::join('students', 'fees_information.student_id', '=', 'students.id')
            ->select(
                'fees_information.*',
                'students.first_name',
                'students.last_name',
                'students.admission_number',
                'students.class',
                'students.image'
            )
            ->get();

        return view('accounts.feescollections', compact('feesInformation'));
    }


    /** Add Fees Collection page */
    public function addFeesCollection()
    {
        $students = Student::select('id', 'first_name', 'last_name', 'class')->get();
        $feesType = FeesType::all();
        return view('accounts.add-fees-collection', compact('students', 'feesType'));
    }

    /** Save new payment */
    public function saveRecord(Request $request)
{
    $request->validate([
        'student_id'    => 'required|exists:students,id',
        'amount_paying' => 'required|numeric|min:1',
        'paid_date'     => 'required|date',
    ]);

    DB::beginTransaction();

    try {
        $student = Student::findOrFail($request->student_id);

        // 🔹 Current values
        $feeAmount    = $student->fee_amount ?? 0;
        $alreadyPaid  = $student->amount_paid ?? 0;
        $newPayment   = $request->amount_paying;

        // 🔹 New totals
        $newTotalPaid = $alreadyPaid + $newPayment;
        $newBalance   = $feeAmount - $newTotalPaid;

        // 🔹 Prevent overpayment
        if ($newBalance < 0) {
            Toastr::error('Payment exceeds total fee amount!', 'Error');
            return redirect()->back()->withInput();
        }

        // 🔹 Save new payment in fees_information
        $payment = new FeesInformation();
        $payment->student_id   = $student->id;
        $payment->student_name = $student->first_name . ' ' . $student->last_name;
        $payment->fees_type    = $request->fees_type ?? 'Tuition Fee';
        $payment->fees_amount  = $newPayment; // Amount just paid
        $payment->paid_date    = $request->paid_date;
        $payment->save();

        // 🔹 Update student record
        $student->amount_paid = $newTotalPaid;
        $student->balance     = $newBalance;
        $student->save();

        DB::commit();

        Toastr::success('Payment recorded successfully!', 'Success');
        return redirect()->back();

    } catch (\Exception $e) {
        DB::rollback();
        Toastr::error('Failed to record payment: ' . $e->getMessage(), 'Error');
        return redirect()->back()->withInput();
    }
}


    /** Student search for Select2 */
    public function search(Request $request)
    {
        $term = $request->get('term', '');

        $students = Student::where('first_name', 'LIKE', "%{$term}%")
            ->orWhere('last_name', 'LIKE', "%{$term}%")
            ->select('id', 'first_name', 'last_name', 'admission_number', 'class')
            ->limit(10)
            ->get();

        $formatted = $students->map(function ($s) {
            return [
                'id' => $s->id,
                'text' => "{$s->first_name} {$s->last_name} (Class {$s->class})",
                'admission_number' => $s->admission_number,
            ];
        });

        return response()->json($formatted);
    }

    /** Get student's fee summary */
    public function getFeesInfo($id)
{
    try {
        // Fetch student record
        $student = Student::findOrFail($id);

        // Fee details from student table
        $feePerTerm = $student->fee_amount ?? 0;
        $totalPaid  = $student->amount_paid ?? 0;

        // Calculate balance (current outstanding)
        $balance = $feePerTerm - $totalPaid;

        return response()->json([
            'success'      => true,
            'student'      => $student->first_name . ' ' . $student->last_name,
            'admission'    => $student->admission_number,
            'class'        => $student->class,
            'fee_per_term' => number_format($feePerTerm, 2),
            'total_paid'   => number_format($totalPaid, 2),
            'balance'      => number_format($balance, 2),
        ]);
    } catch (\Exception $e) {
        \Log::error('Fee info fetch error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Unable to fetch fee info.'
        ]);
    }
}

}
