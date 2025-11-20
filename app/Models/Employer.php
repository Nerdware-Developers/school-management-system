<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employer extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'full_name',
        'gender',
        'date_of_birth',
        'position',
        'department',
        'joining_date',
        'phone_number',
        'email',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'monthly_salary',
        'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'joining_date' => 'date',
        'monthly_salary' => 'decimal:2',
    ];

    /**
     * Get salary payments for this employer
     */
    public function salaryPayments()
    {
        return $this->hasMany(SalaryPayment::class, 'staff_name', 'full_name');
    }
}

