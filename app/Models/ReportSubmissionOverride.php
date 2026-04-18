<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportSubmissionOverride extends Model
{
    protected $fillable = ['user_id', 'date', 'allow_morning', 'allow_evening'];

    protected $casts = [
        'date' => 'date',
        'allow_morning' => 'boolean',
        'allow_evening' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Check if user has override to submit morning report at any time for the given date. */
    public static function hasMorningOverride(int $userId, string $date): bool
    {
        return static::where('user_id', $userId)
            ->where('date', $date)
            ->where('allow_morning', true)
            ->exists();
    }

    /** Check if user has override to submit evening report at any time for the given date. */
    public static function hasEveningOverride(int $userId, string $date): bool
    {
        return static::where('user_id', $userId)
            ->where('date', $date)
            ->where('allow_evening', true)
            ->exists();
    }
}
