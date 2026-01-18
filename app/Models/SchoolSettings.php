<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'financial_year',
        'term_duration_months',
        'default_fee_amount',
        'terms_per_year',
        'academic_year_start_month',
    ];

    protected $casts = [
        'term_duration_months' => 'integer',
        'default_fee_amount' => 'decimal:2',
        'terms_per_year' => 'integer',
    ];

    /**
     * Get the current school settings (singleton pattern)
     */
    public static function getSettings()
    {
        return static::first() ?? static::create([
            'financial_year' => date('Y'),
            'term_duration_months' => 3,
            'default_fee_amount' => 0.00,
            'terms_per_year' => 3,
            'academic_year_start_month' => 'January',
        ]);
    }
}
