<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'date', 'status', 'notes'];

    protected $casts = [
        'date' => 'date',
    ];

    public const STATUS_PRESENT = 'present';
    public const STATUS_ABSENT = 'absent';
    public const STATUS_OFF = 'off';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if a date is an off-day by default (Sunday, odd Saturday, or holiday).
     */
    public static function isOffDay(Carbon|string $date): bool
    {
        $d = $date instanceof Carbon ? $date : Carbon::parse($date);
        // Sunday (0 = Sunday in Carbon)
        if ($d->dayOfWeek === Carbon::SUNDAY) {
            return true;
        }
        // Odd Saturday: 1st, 3rd, 5th Saturday of the month
        if ($d->dayOfWeek === Carbon::SATURDAY) {
            $weekOfMonth = (int) ceil($d->day / 7);
            if (in_array($weekOfMonth, [1, 3, 5], true)) {
                return true;
            }
        }
        // Holiday
        if (Holiday::whereDate('date', $d->toDateString())->exists()) {
            return true;
        }
        return false;
    }

    /**
     * Default status for a date when no record exists.
     */
    public static function defaultStatusForDate(Carbon|string $date): string
    {
        return self::isOffDay($date) ? self::STATUS_OFF : self::STATUS_PRESENT;
    }

    /**
     * Scope for date range.
     */
    public function scopeInDateRange($query, $start, $end)
    {
        return $query->whereBetween('date', [$start, $end]);
    }

    /**
     * Get user IDs of employees who are "present" on the given date.
     */
    public static function getPresentEmployeeIdsForDate(Carbon|string $date): array
    {
        $d = $date instanceof Carbon ? $date : Carbon::parse($date);
        $employees = \App\Models\User::role('employee')->where('is_active', true)->pluck('id');
        $records = self::whereIn('user_id', $employees)->whereDate('date', $d)->get()->keyBy('user_id');
        $present = [];
        foreach ($employees as $uid) {
            $rec = $records->get($uid);
            $status = $rec ? $rec->status : self::defaultStatusForDate($d);
            if ($status === self::STATUS_PRESENT) {
                $present[] = $uid;
            }
        }
        return $present;
    }
}
