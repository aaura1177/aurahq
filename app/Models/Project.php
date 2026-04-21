<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'budget' => 'decimal:2',
        'start_date' => 'date',
        'expected_end_date' => 'date',
        'actual_end_date' => 'date',
    ];

    public const STATUSES = ['planning', 'active', 'on_hold', 'completed', 'cancelled'];

    public const VENTURES = ['aurateria', 'gicogifts', 'aigather', 'medical_ai'];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(Milestone::class)->orderBy('sort_order')->orderBy('id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getMilestoneProgressAttribute(): float
    {
        $total = $this->milestones()->count();
        if ($total === 0) {
            return 0.0;
        }
        $done = $this->milestones()->whereNotNull('completed_at')->count();

        return round(100 * $done / $total, 1);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'planning' => 'slate',
            'active' => 'green',
            'on_hold' => 'amber',
            'completed' => 'blue',
            'cancelled' => 'red',
            default => 'slate',
        };
    }
}
