<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_fee_id',
        'amount',
        'payment_date',
        'payment_method_id',
        'receipt_number',
        'transaction_reference',
        'notes',
        'recorded_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    /**
     * Relationship: Payment belongs to StudentFee
     */
    public function studentFee()
    {
        return $this->belongsTo(StudentFee::class);
    }

    /**
     * Relationship: Payment belongs to PaymentMethod
     */
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * Relationship: Payment recorded by User
     */
    public function recordedByUser()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Get the student through StudentFee
     */
    public function student()
    {
        return $this->hasOneThrough(
            Student::class,
            StudentFee::class,
            'id',
            'id',
            'student_fee_id',
            'student_id'
        );
    }

    /**
     * Generate receipt number automatically
     */
    public static function generateReceiptNumber()
    {
        $prefix = 'RCP-' . date('Y');
        $lastReceipt = self::where('receipt_number', 'like', $prefix . '%')
                          ->latest('id')
                          ->first();
        
        if (!$lastReceipt) {
            $number = 1;
        } else {
            $lastNumber = (int) substr($lastReceipt->receipt_number, -5);
            $number = $lastNumber + 1;
        }
        
        return $prefix . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Scope: Get payments by date range
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    /**
     * Scope: Get payments for student
     */
    public function scopeForStudent($query, $studentId)
    {
        return $query->whereHas('studentFee', function ($q) use ($studentId) {
            $q->where('student_id', $studentId);
        });
    }

    /**
     * Scope: Get payments by method
     */
    public function scopeByMethod($query, $methodId)
    {
        return $query->where('payment_method_id', $methodId);
    }
}
