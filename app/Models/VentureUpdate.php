<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VentureUpdate extends Model
{
    protected $guarded = [];

    public const TYPES = ['update', 'milestone', 'decision', 'blocker'];

    public function venture(): BelongsTo
    {
        return $this->belongsTo(Venture::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            'milestone' => 'fa-flag-checkered',
            'decision' => 'fa-gavel',
            'blocker' => 'fa-triangle-exclamation',
            default => 'fa-circle-info',
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'milestone' => 'text-blue-600',
            'decision' => 'text-purple-600',
            'blocker' => 'text-red-600',
            default => 'text-slate-600',
        };
    }
}
