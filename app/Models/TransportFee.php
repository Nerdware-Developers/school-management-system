<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransportFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'location',
        'amount',
        'is_active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get transport fee for a specific location
     */
    public static function getFeeForLocation(string $location): ?self
    {
        return static::where('location', $location)
            ->where('is_active', true)
            ->first();
    }
}
