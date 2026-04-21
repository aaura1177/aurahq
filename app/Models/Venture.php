<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Venture extends Model
{
    protected $guarded = [];

    protected $casts = [
        'partner_funded' => 'boolean',
    ];

    public const STATUSES = ['active', 'paused', 'planned'];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function updates(): HasMany
    {
        return $this->hasMany(VentureUpdate::class)->latest();
    }

    public function lastUpdate(): HasOne
    {
        return $this->hasOne(VentureUpdate::class)->latestOfMany();
    }

    /**
     * Projects use the same string keys as venture slugs (e.g. aurateria, medical_ai).
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'venture', 'slug');
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'active' => 'green',
            'paused' => 'amber',
            'planned' => 'blue',
            default => 'slate',
        };
    }

    public function getOpenTasksCountAttribute(): int
    {
        return Task::query()
            ->where('is_active', true)
            ->where('status', '!=', 'completed')
            ->whereHas('project', function ($q) {
                $q->where('venture', $this->slug);
            })
            ->count();
    }

    public function getOpenProjectsCountAttribute(): int
    {
        return $this->projects()
            ->where('is_active', true)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->count();
    }

    public function financeReceivedTotal(): float
    {
        return (float) Finance::query()
            ->where('is_active', true)
            ->where('type', 'received')
            ->where('venture', $this->slug)
            ->sum('amount');
    }

    public function financeGivenTotal(): float
    {
        return (float) Finance::query()
            ->where('is_active', true)
            ->where('type', 'given')
            ->where('venture', $this->slug)
            ->sum('amount');
    }
}
