<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        // 🧾 Legal Information
        'first_name',
        'last_name',
        'gender',
        'date_of_birth',
        'roll',
        'class',
        'admission_number',
        'address',
        'image',
        'former_school',
        'residence',
        'term',

        // 👨‍👩‍👧 Parent Information
        'parent_name',
        'parent_number',
        'parent_relationship',
        'parent_email',
        'guardian_name',
        'guardian_number',
        'guardian_email',
        'father_name',
        'father_telephone',
        'mother_name',
        'mother_telephone',
        'occupation',
        'religion',

        // ⚽ Co-Activities
        'sports',
        'clubs',
        
        // 🏥 Medical Information
        'blood_group',
        'known_allergies',
        'medical_condition',
        'doctor_contact',
        'emergency_contact',
        'has_ailment',
        'ailment_details',
        'emergency_contact_name',
        'emergency_contact_telephone',

        // 💰 Financial Information
        'fee_amount',
        'financial_year',
        'amount_paid',
        'fee_type',
        'balance',
        'payment_status',
        'transaction_id',
        'next_due_date',
        'scholarship',
        'sponsor_name',
        
        // 🚌 Transport Information
        'uses_transport',
        'transport_section',
    ];

    public function feeTerms()
    {
        return $this->hasMany(StudentFeeTerm::class);
    }

    public function latestFeeTerm()
    {
        return $this->hasOne(StudentFeeTerm::class)->latestOfMany();
    }

    /**
     * Get the current fee term (latest term by created_at)
     * This is the source of truth for the student's current balance
     */
    public function currentFeeTerm()
    {
        return $this->hasOne(StudentFeeTerm::class)->latestOfMany('created_at');
    }

    /**
     * Accessor for current balance from latest fee term
     * Returns the closing_balance from the latest fee term, or 0 if no term exists
     */
    public function getCurrentBalanceAttribute()
    {
        // Use the relationship to get the latest fee term
        $latestTerm = $this->currentFeeTerm;
        
        if (!$latestTerm) {
            return 0;
        }
        
        // Return the closing_balance, ensuring it's never negative for display purposes
        return max((float) $latestTerm->closing_balance, 0);
    }

    public function busAssignment()
    {
        return $this->hasOne(StudentBusAssignment::class)->where('status', 'active');
    }

    public function busAssignments()
    {
        return $this->hasMany(StudentBusAssignment::class);
    }

    public function examResults()
    {
        return $this->hasMany(ExamResult::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

}
