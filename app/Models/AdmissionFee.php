<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'fee_type',
        'amount',
        'is_active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get total admission fees
     */
    public static function getTotalAdmissionFee(): float
    {
        return static::where('is_active', true)->sum('amount');
    }

    /**
     * Get admission fee by type
     */
    public static function getFeeByType(string $type): ?self
    {
        return static::where('fee_type', $type)
            ->where('is_active', true)
            ->first();
    }
}
