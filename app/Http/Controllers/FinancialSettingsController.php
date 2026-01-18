<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SchoolSettings;
use App\Models\Student;
use App\Models\StudentFeeTerm;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;

class FinancialSettingsController extends Controller
{
    /**
     * Display financial settings page
     */
    public function index()
    {
        $settings = SchoolSettings::getSettings();
        return view('settings.financial', compact('settings'));
    }

    /**
     * Update financial settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'financial_year' => 'required|string|max:25',
            'term_duration_months' => 'required|integer|min:1|max:12',
            'default_fee_amount' => 'required|numeric|min:0',
            'terms_per_year' => 'required|integer|min:1|max:12',
            'academic_year_start_month' => 'required|string|max:20',
            'create_new_terms' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            $settings = SchoolSettings::getSettings();
            $oldFinancialYear = $settings->financial_year;
            $newFinancialYear = $request->financial_year;
            
            // Update settings
            $settings->update($request->only([
                'financial_year',
                'term_duration_months',
                'default_fee_amount',
                'terms_per_year',
                'academic_year_start_month',
            ]));

            // If financial year changed and user wants to create new terms
            if ($oldFinancialYear !== $newFinancialYear && $request->has('create_new_terms') && $request->create_new_terms) {
                $this->createNewTermsForAllStudents($newFinancialYear, $settings->default_fee_amount);
            }

            DB::commit();
            Toastr::success('Financial settings updated successfully!', 'Success');
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Failed to update settings: ' . $e->getMessage(), 'Error');
        }

        return redirect()->back();
    }

    /**
     * Apply settings to all students
     */
    public function applyToAllStudents(Request $request)
    {
        $request->validate([
            'apply_financial_year' => 'nullable|boolean',
            'apply_fee_amount' => 'nullable|boolean',
            'create_new_terms' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            $settings = SchoolSettings::getSettings();
            $updatedStudents = 0;
            $updatedTerms = 0;

            if ($request->apply_financial_year) {
                // Check if we should create new terms (if financial year is different from existing terms)
                $shouldCreateNewTerms = $request->has('create_new_terms') && $request->create_new_terms;
                
                if ($shouldCreateNewTerms) {
                    // Create new terms for all students with balance carry-over
                    $result = $this->createNewTermsForAllStudents($settings->financial_year, $settings->default_fee_amount);
                    $updatedStudents = $result['students_updated'];
                    $updatedTerms = $result['terms_created'];
                } else {
                    // Just update existing records without creating new terms
                    Student::query()->update(['financial_year' => $settings->financial_year]);
                    $updatedStudents++;
                    
                    // Update current terms in student_fee_terms table
                    $updatedTerms += StudentFeeTerm::where('status', 'current')
                        ->update(['academic_year' => $settings->financial_year]);
                }
            }

            if ($request->apply_fee_amount) {
                // Update students table
                Student::query()->update(['fee_amount' => $settings->default_fee_amount]);
                $updatedStudents++;
                
                // Update current terms in student_fee_terms table
                // We need to recalculate closing balance when fee_amount changes
                $currentTerms = StudentFeeTerm::where('status', 'current')->get();
                foreach ($currentTerms as $term) {
                    // Recalculate closing balance: opening + new_fee - amount_paid
                    $newClosingBalance = $term->opening_balance + $settings->default_fee_amount - $term->amount_paid;
                    
                    $term->fee_amount = $settings->default_fee_amount;
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
                    $updatedTerms++;
                }
                
                // Also update student balance based on current term
                $students = Student::with('feeTerms')->get();
                foreach ($students as $student) {
                    $currentTerm = $student->feeTerms()->where('status', 'current')->first();
                    if ($currentTerm) {
                        $student->balance = max($currentTerm->closing_balance, 0);
                        $student->save();
                    }
                }
            }

            DB::commit();
            $message = "Settings applied successfully! ";
            $message .= "Updated {$updatedStudents} student record(s) and {$updatedTerms} term record(s).";
            Toastr::success($message, 'Success');
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Failed to apply financial settings: ' . $e->getMessage());
            Toastr::error('Failed to apply settings: ' . $e->getMessage(), 'Error');
        }

        return redirect()->back();
    }

    /**
     * Create new terms for all students when financial year changes
     * Carries over balance from previous term to new term
     */
    protected function createNewTermsForAllStudents(string $newFinancialYear, float $defaultFeeAmount)
    {
        $students = Student::with(['feeTerms' => function ($query) {
            $query->orderBy('id', 'desc');
        }])->get();

        $termsCreated = 0;
        $studentsUpdated = 0;

        foreach ($students as $student) {
            // Get the current term (status = 'current') or the latest term
            $currentTerm = $student->feeTerms->firstWhere('status', 'current') 
                ?? $student->feeTerms->first();
            
            // Close the current term if it exists
            if ($currentTerm) {
                // Update status based on closing balance
                if ($currentTerm->closing_balance > 0) {
                    $currentTerm->status = 'carried';
                } elseif ($currentTerm->closing_balance < 0) {
                    $currentTerm->status = 'credit';
                } else {
                    $currentTerm->status = 'closed';
                }
                $currentTerm->save();
            }

            // Get the previous term's closing balance (this will be the opening balance for new term)
            $previousClosingBalance = $currentTerm ? $currentTerm->closing_balance : 0;
            
            // Calculate new term's closing balance (opening balance + fee amount - 0 paid)
            $newClosingBalance = $previousClosingBalance + $defaultFeeAmount;
            
            // Determine status for new term
            $newTermStatus = $newClosingBalance > 0 ? 'current' : ($newClosingBalance < 0 ? 'credit' : 'closed');
            
            // Generate term name (Term 1 for new year)
            $termCount = $student->feeTerms()->count() + 1;
            $termName = "Term 1 ({$newFinancialYear})";
            
            // Create new term with carried balance
            $newTerm = $student->feeTerms()->create([
                'term_name' => $termName,
                'academic_year' => $newFinancialYear,
                'fee_amount' => $defaultFeeAmount,
                'amount_paid' => 0,
                'opening_balance' => $previousClosingBalance,
                'closing_balance' => $newClosingBalance,
                'status' => $newTermStatus,
                'notes' => $previousClosingBalance > 0 
                    ? "Balance carried forward from previous term: Ksh " . number_format($previousClosingBalance, 2)
                    : null,
            ]);

            // Update student record
            $student->balance = max($newClosingBalance, 0);
            $student->fee_amount = $defaultFeeAmount;
            $student->financial_year = $newFinancialYear;
            $student->save();

            $termsCreated++;
            $studentsUpdated++;
        }

        return [
            'terms_created' => $termsCreated,
            'students_updated' => $studentsUpdated,
        ];
    }
}
