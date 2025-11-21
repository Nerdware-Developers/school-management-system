<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentBusAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'bus_id',
        'route_id',
        'stop_id',
        'assigned_date',
        'end_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'assigned_date' => 'date',
        'end_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function stop()
    {
        return $this->belongsTo(RouteStop::class, 'stop_id');
    }
}
