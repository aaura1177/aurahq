<?php

namespace App\Support;

use App\Models\Finance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinancePnlReport
{
    public static function monthRangeFromQuery(?string $monthInput): array
    {
        try {
            $monthStart = Carbon::createFromFormat('Y-m', $monthInput ?? Carbon::now()->format('Y-m'))->startOfMonth();
        } catch (\Throwable $e) {
            $monthStart = Carbon::now()->startOfMonth();
        }

        return [$monthStart, $monthStart->copy()->endOfMonth()];
    }

    public static function sumForMonth(Carbon $start, Carbon $end, string $type, string $venture): float
    {
        $q = Finance::query()
            ->where('is_active', true)
            ->where('type', $type)
            ->whereBetween('transaction_date', [$start, $end]);
        if ($venture !== 'all') {
            $q->where('venture', $venture);
        }

        return (float) $q->sum('amount');
    }

    public static function expenseBreakdown(Carbon $start, Carbon $end, string $venture): array
    {
        $q = Finance::query()
            ->where('is_active', true)
            ->where('type', 'given')
            ->whereBetween('transaction_date', [$start, $end]);
        if ($venture !== 'all') {
            $q->where('venture', $venture);
        }

        $rows = $q->selectRaw('COALESCE(NULLIF(category, ""), "__none__") as cat_key, SUM(amount) as total')
            ->groupBy('cat_key')
            ->get();

        $total = (float) $rows->sum('total');
        $out = [];
        foreach ($rows as $row) {
            $key = $row->cat_key === '__none__' ? null : $row->cat_key;
            $amount = (float) $row->total;
            $label = Finance::categoryLabel($key, 'given');
            $out[] = [
                'key' => $key ?? '',
                'label' => $label,
                'amount' => $amount,
                'pct' => $total > 0 ? round(($amount / $total) * 100, 1) : 0.0,
            ];
        }
        usort($out, fn ($a, $b) => $b['amount'] <=> $a['amount']);

        return $out;
    }

    public static function revenueByContact(Carbon $start, Carbon $end, string $venture)
    {
        $q = Finance::query()
            ->join('finance_contacts', 'finances.finance_contact_id', '=', 'finance_contacts.id')
            ->where('finances.is_active', true)
            ->where('finances.type', 'received')
            ->whereBetween('finances.transaction_date', [$start, $end]);

        if ($venture !== 'all') {
            $q->where('finances.venture', $venture);
        }

        return $q->select('finance_contacts.name', DB::raw('SUM(finances.amount) as total'))
            ->groupBy('finance_contacts.id', 'finance_contacts.name')
            ->orderByDesc('total')
            ->get();
    }

    public static function linesForType(Carbon $start, Carbon $end, string $type, string $venture): array
    {
        $q = Finance::query()
            ->where('is_active', true)
            ->where('type', $type)
            ->whereBetween('transaction_date', [$start, $end]);
        if ($venture !== 'all') {
            $q->where('venture', $venture);
        }

        $rows = $q->selectRaw('COALESCE(NULLIF(category, ""), "__none__") as cat_key, SUM(amount) as total')
            ->groupBy('cat_key')
            ->orderByDesc('total')
            ->get();

        $out = [];
        foreach ($rows as $row) {
            $key = $row->cat_key === '__none__' ? null : $row->cat_key;
            $out[] = [
                'label' => Finance::categoryLabel($key, $type),
                'amount' => (float) $row->total,
            ];
        }

        return $out;
    }

    /**
     * @return array{labels: string[], profit: float[]}
     */
    public static function sixMonthProfitSeries(string $venture): array
    {
        $labels = [];
        $profit = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = Carbon::now()->subMonthsNoOverflow($i)->startOfMonth();
            $e = $m->copy()->endOfMonth();
            $r = self::sumForMonth($m, $e, 'received', $venture);
            $ex = self::sumForMonth($m, $e, 'given', $venture);
            $labels[] = $m->format('M Y');
            $profit[] = round($r - $ex, 2);
        }

        return ['labels' => $labels, 'profit' => $profit];
    }
}
