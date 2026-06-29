<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'from_class_id',
        'to_class_id',
        'type',
        'academic_year',
        'promoted_by',
        'promoted_at',
    ];

    protected $casts = [
        'promoted_at' => 'datetime',
    ];

    // Relationships

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function fromClass()
    {
        return $this->belongsTo(ClassModel::class, 'from_class_id');
    }

    public function toClass()
    {
        return $this->belongsTo(ClassModel::class, 'to_class_id');
    }

    public function promotedBy()
    {
        return $this->belongsTo(User::class, 'promoted_by');
    }

    // Scopes

    public function scopePromotions($query)
    {
        return $query->where('type', 'promoted');
    }

    public function scopeRetentions($query)
    {
        return $query->where('type', 'retained');
    }

    public function scopeForAcademicYear($query, string $year)
    {
        return $query->where('academic_year', $year);
    }
}
