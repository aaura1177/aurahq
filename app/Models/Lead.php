<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Lead extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'estimated_value' => 'decimal:2',
        'next_follow_up' => 'date',
        'last_contacted_at' => 'datetime',
        'converted_at' => 'datetime',
    ];

    public const STAGES = ['prospect', 'contacted', 'discovery_call', 'proposal_sent', 'negotiation', 'won', 'lost'];

    /** Stages shown as pipeline columns (excludes closed). */
    public const PIPELINE_STAGES = ['prospect', 'contacted', 'discovery_call', 'proposal_sent', 'negotiation'];

    public const SOURCES = ['linkedin', 'upwork', 'whatsapp', 'referral', 'walk_in', 'facebook', 'website', 'other'];

    public const INDUSTRIES = ['clinic', 'restaurant', 'hotel', 'gym', 'real_estate', 'coaching', 'retail', 'ecommerce', 'saas', 'manufacturing', 'other'];

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function activities()
    {
        return $this->hasMany(LeadActivity::class)->latest();
    }

    public function clients()
    {
        return $this->hasMany(Client::class, 'lead_id');
    }

    public function scopeOverdue($query)
    {
        return $query->whereNotNull('next_follow_up')
            ->whereDate('next_follow_up', '<', Carbon::today())
            ->whereNotIn('stage', ['won', 'lost']);
    }

    public function scopeByStage($query, string $stage)
    {
        return $query->where('stage', $stage);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isOverdue(): bool
    {
        if (! $this->next_follow_up || in_array($this->stage, ['won', 'lost'], true)) {
            return false;
        }

        return $this->next_follow_up->lt(Carbon::today());
    }

    public function getStageLabelAttribute(): string
    {
        return Str::title(str_replace('_', ' ', $this->stage));
    }

    public function getStageColorAttribute(): string
    {
        return match ($this->stage) {
            'prospect' => 'slate',
            'contacted' => 'blue',
            'discovery_call' => 'cyan',
            'proposal_sent' => 'purple',
            'negotiation' => 'orange',
            'won' => 'green',
            'lost' => 'red',
            default => 'slate',
        };
    }
}
