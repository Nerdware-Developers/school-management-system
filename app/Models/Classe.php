<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classe extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_name',
        'class_code',
    ];

    /**
     * Get the teachers who are class teachers for this class
     */
    public function classTeachers()
    {
        return $this->hasMany(Teacher::class, 'class_teacher_id');
    }

    /**
     * Get the teachers who teach subjects in this class
     */
    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_subject_class')
                    ->withPivot('subject_id')
                    ->withTimestamps();
    }
}

