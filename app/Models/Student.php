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
        'is_active',
        'admission_number',
        'admission_date',
        'is_admitted'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'enrollment_date' => 'date',
        'admission_date' => 'date',
        'is_active' => 'boolean',
        'is_admitted' => 'boolean'
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

    /**
     * Relationship: Student has many admission letters
     */
    public function admissionLetters()
    {
        return $this->hasMany(AdmissionLetter::class);
    }

    /**
     * Get the most recent admission letter
     */
    public function latestAdmissionLetter()
    {
        return $this->hasOne(AdmissionLetter::class)
            ->latest('generated_at');
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
