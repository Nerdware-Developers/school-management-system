<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Employer;
use App\Models\SalaryPayment;
use Brian2694\Toastr\Facades\Toastr;

class EmployerController extends Controller
{
    /**
     * Format phone number to ensure it starts with +254
     */
    protected function formatPhoneNumber(string $phone): string
    {
        // Remove all non-digit characters except +
        $phone = preg_replace('/[^\d+]/', '', $phone);
        
        // If it already starts with +254, return as is
        if (strpos($phone, '+254') === 0) {
            return $phone;
        }
        
        // If it starts with 254, add +
        if (strpos($phone, '254') === 0) {
            return '+' . $phone;
        }
        
        // If it starts with 0, replace with +254
        if (strpos($phone, '0') === 0) {
            return '+254' . substr($phone, 1);
        }
        
        // If it's just digits, assume it's a local number starting with 0
        if (preg_match('/^\d+$/', $phone)) {
            if (strpos($phone, '0') === 0) {
                return '+254' . substr($phone, 1);
            }
            // If it doesn't start with 0, add +2540
            return '+254' . $phone;
        }
        
        // Default: add +254 prefix
        return '+254' . preg_replace('/[^\d]/', '', $phone);
    }

    /** List all employers */
    public function index()
    {
        $employers = Employer::orderBy('full_name')->paginate(10);
        return view('employers.index', compact('employers'));
    }

    /** Show add employer form */
    public function create()
    {
        return view('employers.create');
    }

    /** Store new employer */
    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'gender' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'joining_date' => 'nullable|date',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:255',
            'monthly_salary' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $employer = new Employer();
            $requestData = $request->all();
            
            // Format phone number if provided
            if (!empty($requestData['phone_number'])) {
                $requestData['phone_number'] = $this->formatPhoneNumber($requestData['phone_number']);
            }
            
            $employer->fill($requestData);
            
            // Generate employee ID if not provided
            if (!$employer->employee_id) {
                $lastEmployer = Employer::orderBy('id', 'desc')->first();
                $nextId = $lastEmployer ? intval(substr($lastEmployer->employee_id ?? 'EMP000', 3)) + 1 : 1;
                $employer->employee_id = 'EMP' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
            }

            $employer->save();

            DB::commit();
            Toastr::success('Employer added successfully!', 'Success');
            return redirect()->route('employers.index');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error($e);
            Toastr::error('Failed to add employer: ' . $e->getMessage(), 'Error');
            return redirect()->back()->withInput();
        }
    }

    /** Show employer profile */
    public function show($id)
    {
        $employer = Employer::findOrFail($id);
        
        // Get payment history
        $paymentHistory = SalaryPayment::where('staff_name', $employer->full_name)
            ->orderByDesc('payment_date')
            ->orderByDesc('created_at')
            ->get();

        $paymentStats = [
            'total_paid' => $paymentHistory->sum('amount'),
            'total_payments' => $paymentHistory->count(),
            'this_year' => $paymentHistory->filter(function ($payment) {
                return $payment->payment_date && 
                       is_object($payment->payment_date) && 
                       $payment->payment_date->year === now()->year;
            })->sum('amount'),
            'this_month' => $paymentHistory->filter(function ($payment) {
                return $payment->payment_date && 
                       is_object($payment->payment_date) &&
                       $payment->payment_date->year === now()->year &&
                       $payment->payment_date->month === now()->month;
            })->sum('amount'),
        ];

        return view('employers.show', compact('employer', 'paymentHistory', 'paymentStats'));
    }

    /** Show edit form */
    public function edit($id)
    {
        $employer = Employer::findOrFail($id);
        return view('employers.edit', compact('employer'));
    }

    /** Update employer */
    public function update(Request $request, $id)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'gender' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'joining_date' => 'nullable|date',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:255',
            'monthly_salary' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $employer = Employer::findOrFail($id);
            $requestData = $request->all();
            
            // Format phone number if provided
            if (!empty($requestData['phone_number'])) {
                $requestData['phone_number'] = $this->formatPhoneNumber($requestData['phone_number']);
            }
            
            $employer->fill($requestData);
            $employer->save();

            DB::commit();
            Toastr::success('Employer updated successfully!', 'Success');
            return redirect()->route('employers.index');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error($e);
            Toastr::error('Failed to update employer: ' . $e->getMessage(), 'Error');
            return redirect()->back()->withInput();
        }
    }

    /** Delete employer */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $employer = Employer::findOrFail($id);
            $employer->delete();

            DB::commit();
            Toastr::success('Employer deleted successfully!', 'Success');
            return redirect()->route('employers.index');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error($e);
            Toastr::error('Failed to delete employer: ' . $e->getMessage(), 'Error');
            return redirect()->back();
        }
    }
}

