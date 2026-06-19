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

        // Assign fees from class schedule when student is created
        static::created(function ($student) {
            if ($student->class_id) {
                $student->assignFeesFromClass();
            }
        });

        // Update fees when class is changed
        static::updated(function ($student) {
            $originalClassId = $student->getOriginal('class_id');
            if ($originalClassId !== $student->class_id && $student->class_id) {
                // Delete old fees if class changed
                if ($originalClassId) {
                    StudentFee::where('student_id', $student->id)->delete();
                }
                // Assign new fees
                $student->assignFeesFromClass();
            }
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

    /**
     * Relationship: Student has many StudentFees
     */
    public function studentFees()
    {
        return $this->hasMany(StudentFee::class);
    }

    /**
     * Relationship: Student has many Payments through StudentFees
     */
    public function payments()
    {
        return $this->hasManyThrough(Payment::class, StudentFee::class);
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Assign fees to student based on their class
     */
    public function assignFeesFromClass()
    {
        if (!$this->class_id) {
            return;
        }

        // Get the current term and academic year
        $term = config('school.current_term', '1');
        $academicYear = config('school.current_academic_year', null);

        // Get the fee schedule for this class - try with exact match first, then fallback to latest
        $schedule = FeeSchedule::where('class_id', $this->class_id)
            ->where('term', $term);
        
        if ($academicYear) {
            $schedule = $schedule->where('academic_year', $academicYear);
        }
        
        $schedule = $schedule->latest('academic_year')->first();

        if (!$schedule || empty($schedule->fee_amounts)) {
            return;
        }

        // Decode fee_amounts if it's a string (JSON)
        $feeAmounts = $schedule->fee_amounts;
        if (is_string($feeAmounts)) {
            $feeAmounts = json_decode($feeAmounts, true);
        }
        
        if (!is_array($feeAmounts) || empty($feeAmounts)) {
            return;
        }

        // Create StudentFee records for each fee in the schedule
        foreach ($feeAmounts as $feeId => $amount) {
            // Check if this fee already exists for this student in this term
            $existing = StudentFee::where('student_id', $this->id)
                ->where('fee_id', $feeId)
                ->where('term', $term)
                ->first();

            if (!$existing) {
                StudentFee::create([
                    'student_id' => $this->id,
                    'fee_id' => $feeId,
                    'amount' => $amount,
                    'term' => $term,
                    'status' => 'outstanding',
                    'due_date' => $schedule->due_date ?? now()->addMonth(),
                ]);
            }
        }
    }
}
