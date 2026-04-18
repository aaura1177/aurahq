<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportEditGrant extends Model
{
    protected $fillable = ['user_id', 'date', 'expires_at', 'granted_by'];

    protected $casts = [
        'date' => 'date',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function grantedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'granted_by');
    }

    public function scopeValid($query)
    {
        return $query->where('expires_at', '>', now());
    }

    /** Check if employee has a valid (non-expired) edit grant for the given report date. */
    public static function hasValidGrant(int $userId, string $reportDate): bool
    {
        return static::where('user_id', $userId)
            ->where('date', $reportDate)
            ->valid()
            ->exists();
    }
}
