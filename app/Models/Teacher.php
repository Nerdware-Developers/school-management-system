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

    /**
     * Pivot assignments linking teacher, subject, and class.
     */
    public function teachingAssignments()
    {
        return $this->hasMany(TeacherSubjectClass::class);
    }

    /**
     * A collection of "Subject (Class)" labels for display.
     */
    public function getTeachingSummaryAttribute()
    {
        if (!$this->relationLoaded('teachingAssignments')) {
            $assignments = $this->teachingAssignments()->with(['subject', 'class'])->get();
            $this->setRelation('teachingAssignments', $assignments);
        } else {
            $assignments = $this->teachingAssignments;
            $assignments->loadMissing(['subject', 'class']);
        }

        return $assignments->map(function ($assignment) {
            $subject = optional($assignment->subject)->subject_name;
            $class = optional($assignment->class)->class_name;

            if ($subject && $class) {
                return "{$subject} ({$class})";
            }

            return $subject ?? $class;
        })->filter()->values();
    }

    /**
     * Unique class names the teacher teaches in.
     */
    public function getTeachingClassesAttribute()
    {
        if (!$this->relationLoaded('teachingAssignments')) {
            $assignments = $this->teachingAssignments()->with('class')->get();
            $this->setRelation('teachingAssignments', $assignments);
        } else {
            $assignments = $this->teachingAssignments;
            $assignments->loadMissing('class');
        }

        return $assignments->map(function ($assignment) {
            return optional($assignment->class)->class_name;
        })->filter()->unique()->values();
    }
}
