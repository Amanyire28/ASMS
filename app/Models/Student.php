<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($student) {
            $nextId = (static::max('id') ?? 0) + 1;
            $student->student_id = date('Y') . str_pad($nextId, 4, '0', STR_PAD_LEFT);
        });
    }

    protected $fillable = [
        'student_id',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'email',
        'phone',
        'address',
        'parent_name',
        'parent_phone',
        'parent_email',
        'class_id',
        'enrollment_date',
        'photo',
        'is_active'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'enrollment_date' => 'date',
        'is_active' => 'boolean'
    ];

    // Relationships
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function marks()
    {
        return $this->hasMany(Mark::class);
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
