<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'fee_id',
        'amount',
        'term',
        'status',
        'due_date',
        'notes',
        'discount_amount',
        'discount_reason',
        'waived',
        'waived_reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'due_date' => 'date',
        'waived' => 'boolean',
    ];

    /**
     * Relationship: StudentFee belongs to Student
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Relationship: StudentFee belongs to Fee
     */
    public function fee()
    {
        return $this->belongsTo(Fee::class);
    }

    /**
     * Relationship: StudentFee has many Payments
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Calculate amount paid
     */
    public function getAmountPaidAttribute()
    {
        return $this->payments()->sum('amount');
    }

    /**
     * Calculate outstanding balance
     */
    public function getOutstandingAttribute()
    {
        $finalAmount = $this->amount - ($this->discount_amount ?? 0);
        if ($this->waived) {
            return 0;
        }
        return max(0, $finalAmount - $this->amount_paid);
    }

    /**
     * Get payment status
     */
    public function getStatusAttribute($value)
    {
        if ($this->waived) {
            return 'waived';
        }
        
        if ($this->outstanding == 0) {
            return 'paid';
        }
        
        if ($this->amount_paid > 0 && $this->outstanding > 0) {
            return 'partial';
        }
        
        return 'outstanding';
    }

    /**
     * Check if payment is overdue
     */
    public function isOverdue()
    {
        if ($this->waived || $this->outstanding == 0) {
            return false;
        }
        
        return now()->isAfter($this->due_date);
    }

    /**
     * Get days overdue
     */
    public function daysOverdue()
    {
        if (!$this->isOverdue()) {
            return 0;
        }
        
        return now()->diffInDays($this->due_date);
    }

    /**
     * Scope: Get outstanding fees
     */
    public function scopeOutstanding($query)
    {
        return $query->whereRaw('amount - COALESCE(discount_amount, 0) > (SELECT COALESCE(SUM(amount), 0) FROM payments WHERE student_fees.id = payments.student_fee_id)')
                     ->where('waived', false);
    }

    /**
     * Scope: Get overdue fees
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now()->toDateString())
                     ->whereRaw('amount - COALESCE(discount_amount, 0) > (SELECT COALESCE(SUM(amount), 0) FROM payments WHERE student_fees.id = payments.student_fee_id)')
                     ->where('waived', false);
    }

    /**
     * Scope: Get fees by term
     */
    public function scopeByTerm($query, $term)
    {
        return $query->where('term', $term);
    }

    /**
     * Scope: Get fees for student
     */
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }
}
