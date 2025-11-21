<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouteStop extends Model
{
    use HasFactory;

    protected $fillable = [
        'route_id',
        'stop_name',
        'address',
        'pickup_time',
        'dropoff_time',
        'stop_order',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function studentAssignments()
    {
        return $this->hasMany(StudentBusAssignment::class, 'stop_id');
    }
}
