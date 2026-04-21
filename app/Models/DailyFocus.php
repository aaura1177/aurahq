<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyFocus extends Model
{
    /** Laravel would pluralize this model to `daily_foci`; migration uses `daily_focuses`. */
    protected $table = 'daily_focuses';

    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
        'task_1_completed' => 'boolean',
        'task_2_completed' => 'boolean',
        'task_3_completed' => 'boolean',
    ];

    public const ENERGY_LEVELS = ['high', 'medium', 'low'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function task1(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_1_id');
    }

    public function task2(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_2_id');
    }

    public function task3(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_3_id');
    }

    public function getCompletedCountAttribute(): int
    {
        return ($this->task_1_completed ? 1 : 0)
            + ($this->task_2_completed ? 1 : 0)
            + ($this->task_3_completed ? 1 : 0);
    }

    public function getAllCompletedAttribute(): bool
    {
        return $this->task_1_completed && $this->task_2_completed && $this->task_3_completed;
    }

    /**
     * Consecutive past days (from yesterday backward) where all three tasks were completed.
     */
    public static function currentStreak(int $userId): int
    {
        $streak = 0;
        $date = now()->subDay()->startOfDay();

        while (true) {
            $focus = static::query()
                ->where('user_id', $userId)
                ->whereDate('date', $date->format('Y-m-d'))
                ->first();

            if ($focus && $focus->all_completed) {
                $streak++;
                $date = $date->copy()->subDay();
            } else {
                break;
            }
        }

        return $streak;
    }
}
