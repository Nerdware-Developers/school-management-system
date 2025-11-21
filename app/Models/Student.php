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

        // 👨‍👩‍👧 Parent Information
        'parent_name',
        'parent_number',
        'parent_relationship',
        'parent_email',
        'guardian_name',
        'guardian_number',
        'guardian_email',

        // ⚽ Co-Activities
        'sports',
        'clubs',
        
        // 🏥 Medical Information
        'blood_group',
        'known_allergies',
        'medical_condition',
        'doctor_contact',
        'emergency_contact',

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
    ];

    public function feeTerms()
    {
        return $this->hasMany(StudentFeeTerm::class);
    }

    public function latestFeeTerm()
    {
        return $this->hasOne(StudentFeeTerm::class)->latestOfMany();
    }

    public function busAssignment()
    {
        return $this->hasOne(StudentBusAssignment::class)->where('status', 'active');
    }

    public function busAssignments()
    {
        return $this->hasMany(StudentBusAssignment::class);
    }

}
