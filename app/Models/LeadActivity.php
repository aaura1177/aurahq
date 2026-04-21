<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadActivity extends Model
{
    protected $guarded = [];

    protected $casts = [
        'metadata' => 'array',
    ];

    public const TYPES = ['note', 'call', 'whatsapp', 'email', 'meeting', 'follow_up', 'stage_change', 'proposal_sent'];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Font Awesome 6 classes (layout loads all.min.css — icons need fa-solid / fa-brands prefix).
     */
    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            'call' => 'fa-solid fa-phone',
            'whatsapp' => 'fa-brands fa-whatsapp',
            'email' => 'fa-solid fa-envelope',
            'meeting' => 'fa-solid fa-handshake',
            'stage_change' => 'fa-solid fa-arrow-right',
            'proposal_sent' => 'fa-solid fa-file-lines',
            'follow_up' => 'fa-solid fa-clock',
            default => 'fa-solid fa-note-sticky',
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'call' => 'blue',
            'whatsapp' => 'green',
            'email' => 'purple',
            'meeting' => 'cyan',
            'stage_change' => 'orange',
            'proposal_sent' => 'indigo',
            'follow_up' => 'amber',
            default => 'slate',
        };
    }
}
