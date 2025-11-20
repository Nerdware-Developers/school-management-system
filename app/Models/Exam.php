<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_name', 'term', 'exam_type', 'class_id', 'subject', 'total_marks', 'exam_date'
    ];

    public function class()
    {
        return $this->belongsTo(Classe::class, 'class_id');
    }

    /**
     * Students assigned to this exam (for student-specific exams)
     */
    public function students()
    {
        return $this->belongsToMany(Student::class, 'exam_student');
    }

    /**
     * Exam results for this exam
     */
    public function results()
    {
        return $this->hasMany(ExamResult::class);
    }
}
