<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mark extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'subject_id',
        'class_id',
        'term',
        'academic_year',
        'exam_type',
        'marks_obtained',
        'total_marks',
        'grade',
        'remarks'
    ];

    protected $casts = [
        'marks_obtained' => 'decimal:2',
        'total_marks' => 'decimal:2'
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    // Accessors
    public function getPercentageAttribute()
    {
        return round(($this->marks_obtained / $this->total_marks) * 100, 2);
    }

    // Mutators
    public function setGradeAttribute($value)
    {
        // Auto-calculate grade based on percentage if not provided
        if (empty($value)) {
            $percentage = $this->getPercentageAttribute();
            
            if ($percentage >= 90) $this->attributes['grade'] = 'A+';
            elseif ($percentage >= 80) $this->attributes['grade'] = 'A';
            elseif ($percentage >= 70) $this->attributes['grade'] = 'B+';
            elseif ($percentage >= 60) $this->attributes['grade'] = 'B';
            elseif ($percentage >= 50) $this->attributes['grade'] = 'C+';
            elseif ($percentage >= 40) $this->attributes['grade'] = 'C';
            elseif ($percentage >= 30) $this->attributes['grade'] = 'D';
            else $this->attributes['grade'] = 'F';
        } else {
            $this->attributes['grade'] = $value;
        }
    }
}
