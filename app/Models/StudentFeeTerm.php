<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentFeeTerm extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'term_name',
        'academic_year',
        'fee_amount',
        'amount_paid',
        'opening_balance',
        'closing_balance',
        'status',
        'last_payment_method',
        'last_payment_reference',
        'last_payment_at',
        'notes',
    ];

    protected $casts = [
        'fee_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'opening_balance' => 'decimal:2',
        'closing_balance' => 'decimal:2',
        'last_payment_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function payments()
    {
        return $this->hasMany(FeesInformation::class, 'student_fee_term_id');
    }
}

