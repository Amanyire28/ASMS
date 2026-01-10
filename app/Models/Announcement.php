<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'type',
        'is_active',
        'valid_until',
        'created_by',
    ];

    protected $casts = [
        'valid_until' => 'date',
        'is_active' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('valid_until')
                          ->orWhere('valid_until', '>=', now()->toDateString());
                    });
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function isExpired()
    {
        return $this->valid_until && $this->valid_until < now()->toDateString();
    }

    public function getTypeBadgeClass()
    {
        return match($this->type) {
            'urgent' => 'bg-danger',
            'academic' => 'bg-primary',
            'event' => 'bg-success',
            'general' => 'bg-secondary',
            default => 'bg-secondary'
        };
    }
}
