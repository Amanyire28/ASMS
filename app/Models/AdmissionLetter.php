<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionLetter extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'generated_at',
        'file_path',
        'remarks',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: Letter belongs to Student
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the most recent admission letter for a student
     */
    public static function getLatestForStudent(Student $student)
    {
        return self::where('student_id', $student->id)
            ->latest('generated_at')
            ->first();
    }
}
