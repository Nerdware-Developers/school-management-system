<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;
    protected $fillable = [
        'subject_id',
        'subject_name',
        'teacher_name',
        'class',
    ];

    /**
     * Get teachers assigned to this subject via pivot table
     */
    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_subject_class', 'subject_id', 'teacher_id')
                    ->withPivot('class_id')
                    ->withTimestamps();
    }

    /**
     * Get teaching assignments (pivot records)
     */
    public function teachingAssignments()
    {
        return $this->hasMany(\App\Models\TeacherSubjectClass::class, 'subject_id');
    }

    /**
     * Get the primary teacher name (from pivot table or fallback to teacher_name field)
     */
    public function getPrimaryTeacherNameAttribute()
    {
        // First try to get from pivot table
        $assignment = $this->teachingAssignments()->with('teacher')->first();
        if ($assignment && $assignment->teacher) {
            return $assignment->teacher->full_name;
        }
        
        // Fallback to old teacher_name field
        return $this->teacher_name;
    }

    /** auto generate id */
    protected static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            // Only generate if not already set
            if (empty($model->subject_id)) {
                $lastSubject = self::orderBy('subject_id', 'desc')->first();

                if ($lastSubject && !empty($lastSubject->subject_id) && preg_match('/PRE(\d+)/', $lastSubject->subject_id, $matches)) {
                    $latestID = intval($matches[1]);
                    $nextID = $latestID + 1;
                } else {
                    $nextID = 1;
                }
                
                $model->subject_id = 'PRE' . str_pad($nextID, 3, '0', STR_PAD_LEFT);
                
                // Ensure uniqueness
                while (self::where('subject_id', $model->subject_id)->exists()) {
                    $nextID++;
                    $model->subject_id = 'PRE' . str_pad($nextID, 3, '0', STR_PAD_LEFT);
                }
            }
        });
    }
}
