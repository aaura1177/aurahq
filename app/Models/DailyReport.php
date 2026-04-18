<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'date',
        'morning_submitted_at', 'morning_note', 'morning_task_ids', 'morning_task_notes',
        'evening_submitted_at', 'evening_note', 'evening_task_ids', 'evening_task_notes',
    ];

    protected $casts = [
        'date' => 'date',
        'morning_submitted_at' => 'datetime',
        'evening_submitted_at' => 'datetime',
        'morning_task_ids' => 'array',
        'evening_task_ids' => 'array',
        'morning_task_notes' => 'array',
        'evening_task_notes' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function morningTasks()
    {
        $ids = $this->morning_task_ids ?? [];
        return empty($ids) ? collect() : Task::whereIn('id', $ids)->get();
    }

    public function eveningTasks()
    {
        $ids = $this->evening_task_ids ?? [];
        return empty($ids) ? collect() : Task::whereIn('id', $ids)->get();
    }

    /** Get note for a task in morning report (task_notes keyed by task id as string). */
    public function getMorningTaskNote(int $taskId): string
    {
        $notes = $this->morning_task_notes ?? [];
        return $notes[(string) $taskId] ?? '';
    }

    /** Get note for a task in evening report. */
    public function getEveningTaskNote(int $taskId): string
    {
        $notes = $this->evening_task_notes ?? [];
        return $notes[(string) $taskId] ?? '';
    }

    public function scopeInDateRange($query, $start, $end)
    {
        return $query->whereBetween('date', [$start, $end]);
    }

    /** Report windows in Indian time (IST). Submit till 11:00 AM and till 5:15 PM. */
    public static function isWithinMorningWindow(Carbon $when = null): bool
    {
        $t = ($when ?? now())->setTimezone('Asia/Kolkata');
        $hour = (int) $t->format('G');
        $min = (int) $t->format('i');
        $mins = $hour * 60 + $min;
        return $mins >= 10 * 60 && $mins < 11 * 60; // 10:00 - 11:00
    }

    public static function isWithinEveningWindow(Carbon $when = null): bool
    {
        $t = ($when ?? now())->setTimezone('Asia/Kolkata');
        $hour = (int) $t->format('G');
        $min = (int) $t->format('i');
        $mins = $hour * 60 + $min;
        return $mins >= 16 * 60 + 30 && $mins < 17 * 60 + 15; // 16:30 - 17:15
    }

    /** Past morning deadline 11:00 IST. */
    public static function isPastMorningDeadline(Carbon $when = null): bool
    {
        $t = ($when ?? now())->setTimezone('Asia/Kolkata');
        return $t->format('H:i') >= '11:00';
    }

    /** Past evening deadline 17:15 IST. */
    public static function isPastEveningDeadline(Carbon $when = null): bool
    {
        $t = ($when ?? now())->setTimezone('Asia/Kolkata');
        return $t->format('H:i') >= '17:15';
    }
}
