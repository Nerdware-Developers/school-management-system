<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\FeesType;
use App\Models\FeesInformation;
use App\Models\Student;
use App\Models\StudentFeeTerm;
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
            ->orderByDesc('fees_information.id')
            ->paginate(5);

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
        $student = Student::with(['feeTerms' => function ($q) {
            $q->orderByDesc('created_at');
        }])->findOrFail($request->student_id);

        $currentTerm = $student->feeTerms->firstWhere('status', 'current')
            ?? $student->feeTerms->first();

        if (!$currentTerm) {
            Toastr::error('No active term found for this student. Please create a term first.', 'Error');
            return redirect()->back()->withInput();
        }

        // 🔹 Current values for this term only
        $feeAmount    = (float) $currentTerm->fee_amount;
        $alreadyPaid  = (float) $currentTerm->amount_paid;
        $newPayment   = $request->amount_paying;

        // 🔹 New totals (allow overpayment; balance will clamp to 0)
        $newTotalPaid = $alreadyPaid + $newPayment;
        $newBalance   = $feeAmount - $newTotalPaid;

        // 🔹 Save new payment in fees_information (audit trail)
        $payment = new FeesInformation();
        $payment->student_id         = $student->id;
        $payment->student_fee_term_id = $currentTerm->id;
        $payment->student_name       = $student->first_name . ' ' . $student->last_name;
        $payment->fees_type          = $request->fees_type ?? 'Tuition Fee';
        $payment->fees_amount        = $newPayment; // Amount just paid for this term
        $payment->paid_date          = $request->paid_date;
        $payment->save();

        // 🔹 Update current term
        $currentTerm->amount_paid = $newTotalPaid;
        $currentTerm->closing_balance = $newBalance;
        if ($currentTerm->closing_balance > 0) {
            $currentTerm->status = 'current';
        } elseif ($currentTerm->closing_balance < 0) {
            $currentTerm->status = 'credit';
        } else {
            $currentTerm->status = 'closed';
        }
        $currentTerm->save();

        // 🔹 Update student snapshot (for quick overview)
        $student->amount_paid = $newTotalPaid;
        $student->balance     = max($currentTerm->closing_balance, 0);
        $student->fee_amount  = $feeAmount;
        $student->financial_year = $currentTerm->academic_year;
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
        // Fetch student with term data
        $student = Student::with(['feeTerms' => function ($q) {
            $q->orderByDesc('created_at');
        }])->findOrFail($id);

        $currentTerm = $student->feeTerms->firstWhere('status', 'current')
            ?? $student->feeTerms->first();

        $credit = 0;
        if ($currentTerm) {
            // Use term-based finance
            $feePerTerm = (float) $currentTerm->fee_amount;
            $totalPaid  = (float) $currentTerm->amount_paid;
            $balance    = (float) $currentTerm->closing_balance;
            if ($balance < 0) {
                $credit = abs($balance);
            }
        } else {
            // Fallback to legacy student-level amounts if no term exists
            $feePerTerm = (float) ($student->fee_amount ?? 0);
            $totalPaid  = (float) ($student->amount_paid ?? 0);
            $balance    = $feePerTerm - $totalPaid;
        }

        return response()->json([
            'success'      => true,
            'student'      => $student->first_name . ' ' . $student->last_name,
            'admission'    => $student->admission_number,
            'class'        => $student->class,
            'fee_per_term' => number_format($feePerTerm, 2),
            'total_paid'   => number_format($totalPaid, 2),
            'balance'      => number_format(max($balance, 0), 2),
            'credit'       => number_format($credit, 2),
            'term_name'    => $currentTerm->term_name ?? null,
            'academic_year'=> $currentTerm->academic_year ?? null,
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
