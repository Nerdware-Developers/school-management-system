<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherSubjectClass extends Model
{
    use HasFactory;

    protected $table = 'teacher_subject_class';

    protected $fillable = [
        'teacher_id',
        'subject_id',
        'class_id',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function class()
    {
        return $this->belongsTo(Classe::class, 'class_id');
    }
}

