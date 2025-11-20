<?php

namespace App\Http\Controllers;

use App\Models\SalaryPayment;
use App\Models\Teacher;
use App\Models\Employer;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalaryController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month');

        $salaryQuery = SalaryPayment::orderByDesc('payment_date');

        if ($month) {
            $salaryQuery->where('month_reference', $month);
        }

        $salaryPayments = $salaryQuery->paginate(10)->withQueryString();

        $monthlyTotal = (clone $salaryQuery)->sum('amount');
        $overallTotal = SalaryPayment::sum('amount');

        return view('accounts.salary.index', compact('salaryPayments', 'month', 'monthlyTotal', 'overallTotal'));
    }

    public function create()
    {
        return view('accounts.salary.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'staff_name' => 'required|string|max:255',
            'role' => 'nullable|string|max:100',
            'month_reference' => 'nullable|string|max:25',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string|max:100',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        SalaryPayment::create($validated);

        Toastr::success('Salary payment recorded successfully.', 'Success');
        return redirect()->route('account/salary');
    }

    /**
     * Search for staff names (teachers and employers)
     */
    public function searchStaff(Request $request)
    {
        $query = $request->input('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $staff = collect();

        // Get teachers
        $teachers = Teacher::where('full_name', 'like', '%' . $query . '%')
            ->select('full_name', 'user_id as id', DB::raw("'Teacher' as type"))
            ->get();

        // Get employers
        $employers = Employer::where('full_name', 'like', '%' . $query . '%')
            ->select('full_name', 'employee_id as id', DB::raw("'Employer' as type"))
            ->get();

        // Combine and format results
        $staff = $teachers->concat($employers)->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->full_name,
                'type' => $item->type,
                'display' => $item->full_name . ' (' . $item->type . ')'
            ];
        })->unique('name')->values();

        return response()->json($staff);
    }
}

