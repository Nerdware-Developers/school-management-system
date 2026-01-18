<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\FeesType;
use App\Models\FeesInformation;
use App\Models\Student;
use App\Models\StudentFeeTerm;
use App\Models\Expense;
use App\Models\SalaryPayment;
use App\Models\PaymentTransaction;
use Carbon\Carbon;
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
        \Log::error('Payment record failed: ' . $e->getMessage());
        Toastr::error('Failed to record payment. Please try again.', 'Error');
        return redirect()->back()->withInput();
    }
}


    /** Student search for Select2 */
    public function search(Request $request)
    {
        $request->validate([
            'term' => 'nullable|string|max:255',
        ]);
        
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
            // Validate ID parameter
            if (!is_numeric($id) || $id <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid student ID.'
                ], 400);
            }
            
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

    /**
     * Finance Overview - Show financial summary
     */
    public function financeOverview()
    {
        $now = Carbon::now();
        $selectedYear = request()->input('year', $now->year);
        
        // Total Paid (Income) - All time or for selected year
        $totalPaid = FeesInformation::when($selectedYear, function($query) use ($selectedYear) {
                $query->whereYear('paid_date', $selectedYear);
            })
            ->sum('fees_amount');
        
        // Add online payments
        $onlinePayments = PaymentTransaction::when($selectedYear, function($query) use ($selectedYear) {
                $query->whereYear('created_at', $selectedYear);
            })
            ->where('status', 'completed')
            ->sum('amount');
        
        $totalPaid += $onlinePayments;

        // Total Expenses
        $totalExpenses = Expense::when($selectedYear, function($query) use ($selectedYear) {
                $query->whereYear('expense_date', $selectedYear);
            })
            ->sum('amount');

        // Total Salary Payments
        $totalSalaries = SalaryPayment::when($selectedYear, function($query) use ($selectedYear) {
                $query->whereYear('payment_date', $selectedYear);
            })
            ->sum('amount');

        // Net Profit/Loss
        $netProfit = $totalPaid - ($totalExpenses + $totalSalaries);
        $profitMargin = $totalPaid > 0 ? ($netProfit / $totalPaid) * 100 : 0;

        // Total Expected Fees
        $totalExpected = StudentFeeTerm::when($selectedYear, function($query) use ($selectedYear) {
                $query->where('academic_year', 'like', "%{$selectedYear}%");
            })
            ->sum('fee_amount');

        // Total Outstanding
        $totalOutstanding = StudentFeeTerm::when($selectedYear, function($query) use ($selectedYear) {
                $query->where('academic_year', 'like', "%{$selectedYear}%");
            })
            ->where('closing_balance', '>', 0)
            ->sum('closing_balance');

        // Collection Rate
        $collectionRate = $totalExpected > 0 ? ($totalPaid / $totalExpected) * 100 : 0;

        // Get available years
        $availableYears = collect();
        $yearsFromFees = FeesInformation::selectRaw('YEAR(paid_date) as year')
            ->distinct()
            ->pluck('year');
        $yearsFromTerms = StudentFeeTerm::selectRaw('SUBSTRING_INDEX(academic_year, "-", 1) as year')
            ->distinct()
            ->pluck('year');
        $availableYears = $yearsFromFees->merge($yearsFromTerms)->unique()->sort()->values();

        // Term Statistics
        $termStats = StudentFeeTerm::when($selectedYear, function($query) use ($selectedYear) {
                $query->where('academic_year', 'like', "%{$selectedYear}%");
            })
            ->selectRaw('
                term_name,
                academic_year,
                SUM(fee_amount) as expected_total,
                SUM(amount_paid) as paid_total,
                SUM(CASE WHEN closing_balance > 0 THEN closing_balance ELSE 0 END) as outstanding_total,
                SUM(CASE WHEN closing_balance < 0 THEN ABS(closing_balance) ELSE 0 END) as credit_total,
                COUNT(DISTINCT student_id) as students_count
            ')
            ->groupBy('term_name', 'academic_year')
            ->orderBy('academic_year')
            ->orderBy('term_name')
            ->get();

        // Top Students by Payment
        $topStudents = FeesInformation::when($selectedYear, function($query) use ($selectedYear) {
                $query->whereYear('paid_date', $selectedYear);
            })
            ->selectRaw('student_name, SUM(fees_amount) as total_paid')
            ->groupBy('student_name')
            ->orderByDesc('total_paid')
            ->limit(10)
            ->get();

        // Monthly Payments
        $monthlyPayments = FeesInformation::when($selectedYear, function($query) use ($selectedYear) {
                $query->whereYear('paid_date', $selectedYear);
            })
            ->selectRaw('
                DATE_FORMAT(paid_date, "%Y-%m") as month_key,
                DATE_FORMAT(paid_date, "%b %Y") as month_label,
                SUM(fees_amount) as total
            ')
            ->groupBy('month_key', 'month_label')
            ->orderBy('month_key')
            ->get();

        // Monthly Salaries
        $monthlySalaries = SalaryPayment::when($selectedYear, function($query) use ($selectedYear) {
                $query->whereYear('payment_date', $selectedYear);
            })
            ->selectRaw('
                DATE_FORMAT(payment_date, "%Y-%m") as month_key,
                DATE_FORMAT(payment_date, "%b %Y") as month_label,
                SUM(amount) as total
            ')
            ->groupBy('month_key', 'month_label')
            ->orderBy('month_key')
            ->get();

        // Monthly Expenses
        $monthlyExpenses = Expense::when($selectedYear, function($query) use ($selectedYear) {
                $query->whereYear('expense_date', $selectedYear);
            })
            ->selectRaw('
                DATE_FORMAT(expense_date, "%Y-%m") as month_key,
                DATE_FORMAT(expense_date, "%b %Y") as month_label,
                SUM(amount) as total
            ')
            ->groupBy('month_key', 'month_label')
            ->orderBy('month_key')
            ->get();

        // Expense Categories
        $expenseCategories = Expense::when($selectedYear, function($query) use ($selectedYear) {
                $query->whereYear('expense_date', $selectedYear);
            })
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        // Payment Methods (Fee Types)
        $paymentMethods = FeesInformation::when($selectedYear, function($query) use ($selectedYear) {
                $query->whereYear('paid_date', $selectedYear);
            })
            ->selectRaw('fees_type, SUM(fees_amount) as total')
            ->groupBy('fees_type')
            ->orderByDesc('total')
            ->get();

        return view('accounts.finance-overview', compact(
            'totalPaid',
            'totalExpenses',
            'totalSalaries',
            'netProfit',
            'profitMargin',
            'totalExpected',
            'totalOutstanding',
            'collectionRate',
            'selectedYear',
            'availableYears',
            'termStats',
            'topStudents',
            'monthlyPayments',
            'monthlySalaries',
            'monthlyExpenses',
            'expenseCategories',
            'paymentMethods'
        ));
    }

    /**
     * Export students by balance filter (simplified - only name, admission, balance)
     */
    public function exportStudentsByBalance(Request $request)
    {
        $query = \App\Models\Student::query();
        $filters = [];

        // Filter by balance
        if ($request->has('balance_operator') && $request->balance_operator != '') {
            $operator = $request->balance_operator;
            $amount = $request->has('balance_amount') && $request->balance_amount != '' ? (float) $request->balance_amount : 0;

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
        $students = $query->orderBy('balance', 'desc')->orderBy('first_name')->get();

        // Set headers for CSV download
        $filterSuffix = !empty($filters) ? '_' . implode('_', $filters) : '';
        $filename = 'students_balance' . $filterSuffix . '_' . date('Y-m-d_His') . '.csv';
        
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
            
            // Add CSV headers - simplified: only name, admission, balance
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
}
