<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bus extends Model
{
    use HasFactory;

    protected $fillable = [
        'bus_number',
        'bus_name',
        'driver_name',
        'driver_phone',
        'driver_license',
        'conductor_name',
        'conductor_phone',
        'capacity',
        'vehicle_type',
        'registration_number',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function routes()
    {
        return $this->belongsToMany(Route::class, 'bus_routes')
                    ->withPivot('is_active')
                    ->withTimestamps();
    }

    public function studentAssignments()
    {
        return $this->hasMany(StudentBusAssignment::class);
    }

    public function activeAssignments()
    {
        return $this->hasMany(StudentBusAssignment::class)->where('status', 'active');
    }
}
