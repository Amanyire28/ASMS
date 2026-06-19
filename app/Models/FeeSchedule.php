<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'term',
        'amount',
        'due_date',
        'academic_year',
        'fee_amounts',
        'total_amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'due_date' => 'date',
        'fee_amounts' => 'array',
    ];

    /**
     * Relationship: FeeSchedule belongs to ClassModel
     */
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    /**
     * Get all fees in this schedule
     */
    public function getFees()
    {
        if (empty($this->fee_amounts)) {
            return collect();
        }

        return Fee::whereIn('id', array_keys($this->fee_amounts))->get();
    }

    /**
     * Get fee amounts with fee details
     */
    public function getFeeDetails()
    {
        $details = [];
        if (!empty($this->fee_amounts)) {
            foreach ($this->fee_amounts as $feeId => $amount) {
                $fee = Fee::find($feeId);
                if ($fee) {
                    $details[] = [
                        'fee' => $fee,
                        'amount' => $amount,
                    ];
                }
            }
        }
        return $details;
    }

    /**
     * Scope: Get schedules for class
     */
    public function scopeForClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    /**
     * Scope: Get schedules for term
     */
    public function scopeForTerm($query, $term)
    {
        return $query->where('term', $term);
    }

    /**
     * Scope: Get schedules for academic year
     */
    public function scopeForAcademicYear($query, $year)
    {
        return $query->where('academic_year', $year);
    }

    /**
     * Scope: Get active schedules
     */
    public function scopeActive($query)
    {
        return $query->where('academic_year', config('school.academic_year', date('Y') . '/' . (date('Y') + 1)));
    }
}
