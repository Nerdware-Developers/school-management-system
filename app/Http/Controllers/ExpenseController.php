<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month');

        $expensesQuery = Expense::orderByDesc('expense_date');

        if ($month) {
            $expensesQuery->whereMonth('expense_date', substr($month, 5, 2))
                ->whereYear('expense_date', substr($month, 0, 4));
        }

        $expenses = $expensesQuery->paginate(10)->withQueryString();

        $monthlyTotal = (clone $expensesQuery)->sum('amount');
        $overallTotal = Expense::sum('amount');

        return view('accounts.expenses.index', compact('expenses', 'month', 'monthlyTotal', 'overallTotal'));
    }

    public function create()
    {
        return view('accounts.expenses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'paid_to' => 'nullable|string|max:255',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        Expense::create($validated);

        Toastr::success('Expense recorded successfully.', 'Success');
        return redirect()->route('account/expenses');
    }
}

