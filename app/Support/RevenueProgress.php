<?php

namespace App\Support;

use App\Models\Finance;
use App\Models\RevenueTarget;
use Carbon\Carbon;

class RevenueProgress
{
    const GOAL = 85000000; // ₹8.5 crore = $1M

    public static function actualForMonth(Carbon $month): float
    {
        return (float) Finance::where('type', 'received')
            ->where('is_active', true)
            ->whereYear('transaction_date', $month->year)
            ->whereMonth('transaction_date', $month->month)
            ->sum('amount');
    }

    public static function currentMonth(): array
    {
        $now = Carbon::now('Asia/Kolkata')->startOfMonth();
        $target = RevenueTarget::whereDate('month', $now)->first();
        $targetAmt = $target ? (float) $target->target_amount : 0;
        $actual = self::actualForMonth($now);
        $daysInMonth = $now->copy()->endOfMonth()->day;
        $dayNow = Carbon::now('Asia/Kolkata')->day;

        return [
            'target' => $targetAmt,
            'actual' => $actual,
            'gap' => max($targetAmt - $actual, 0),
            'pct' => $targetAmt > 0 ? round(($actual / $targetAmt) * 100) : 0,
            'days_left' => max($daysInMonth - $dayNow, 0),
        ];
    }

    public static function towardMillion(): array
    {
        $totalActual = (float) Finance::where('type', 'received')
            ->where('is_active', true)
            ->sum('amount');

        return [
            'goal' => self::GOAL,
            'target_sum' => (float) RevenueTarget::sum('target_amount'),
            'actual' => $totalActual,
            'pct' => $totalActual > 0 ? round(($totalActual / self::GOAL) * 100, 1) : 0,
        ];
    }

    // Formats ₹ in Indian style: 8500000 -> "₹85.0L", 11475000 -> "₹1.15Cr"
    public static function inr(float $n): string
    {
        if ($n >= 10000000) {
            return '₹'.number_format($n / 10000000, 2).'Cr';
        }
        if ($n >= 100000) {
            return '₹'.number_format($n / 100000, 1).'L';
        }

        return '₹'.number_format($n);
    }
}
