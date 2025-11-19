<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_name', 'term', 'class_id', 'subject', 'total_marks', 'exam_date'
    ];

    public function class()
    {
        return $this->belongsTo(Classe::class, 'class_id');
    }
}
