<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;

    protected $fillable = [
        'route_name',
        'description',
        'fare',
        'distance_km',
        'start_time',
        'end_time',
        'is_active',
    ];

    protected $casts = [
        'fare' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function stops()
    {
        return $this->hasMany(RouteStop::class)->orderBy('stop_order');
    }

    public function buses()
    {
        return $this->belongsToMany(Bus::class, 'bus_routes')
                    ->withPivot('is_active')
                    ->withTimestamps();
    }

    public function studentAssignments()
    {
        return $this->hasMany(StudentBusAssignment::class);
    }
}
