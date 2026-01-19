<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradeFee extends Model
{
    use HasFactory;

    protected $table = 'grade_fee_structure';

    protected $fillable = [
        'grade',
        'tuition_fee',
        'exam_fee',
        'total_fee',
        'is_active',
    ];

    protected $casts = [
        'tuition_fee' => 'decimal:2',
        'exam_fee' => 'decimal:2',
        'total_fee' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Calculate and set total_fee before saving
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($gradeFee) {
            $gradeFee->total_fee = $gradeFee->tuition_fee + $gradeFee->exam_fee;
        });
    }

    /**
     * Get fee for a specific grade
     */
    public static function getFeeForGrade(string $grade): ?self
    {
        return static::where('grade', $grade)
            ->where('is_active', true)
            ->first();
    }
}
