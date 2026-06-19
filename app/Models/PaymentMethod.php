<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relationship: PaymentMethod has many Payments
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Scope: Get active payment methods
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
