<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;
    protected $fillable = [
        'teacher_id',
        'full_name',
        'gender',
        'date_of_birth',
        'mobile',
        'joining_date',
        'qualification',
        'experience',
        'username',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'class_teacher_id',
    ];

    /**
     * Get the class where this teacher is a class teacher
     */
    public function classTeacher()
    {
        return $this->belongsTo(Classe::class, 'class_teacher_id');
    }

    /**
     * Get the subjects and classes this teacher teaches
     */
    public function subjectClasses()
    {
        return $this->belongsToMany(Classe::class, 'teacher_subject_class', 'teacher_id', 'class_id')
                    ->withPivot('subject_id')
                    ->withTimestamps();
    }

    /**
     * Get the subjects this teacher teaches
     */
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'teacher_subject_class', 'teacher_id', 'subject_id')
                    ->withPivot('class_id')
                    ->withTimestamps();
    }
}
