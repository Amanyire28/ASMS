<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category',
        'type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relationship: Fee has many StudentFees
     */
    public function studentFees()
    {
        return $this->hasMany(StudentFee::class);
    }

    /**
     * Relationship: Fee has many FeeSchedules
     */
    public function feeSchedules()
    {
        return $this->hasMany(FeeSchedule::class);
    }

    /**
     * Scope: Get active fees only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Get fees by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get unique fee categories
     */
    public static function getCategories()
    {
        return self::distinct()->pluck('category')->filter()->values()->toArray();
    }
}
