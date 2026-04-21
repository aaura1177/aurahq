<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\ApiJson;
use App\Models\Finance;
use App\Support\FinancePnlReport;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FinanceDashboardApiController extends Controller
{
    public function index(Request $request)
    {
        $monthInput = $request->query('month', Carbon::now()->format('Y-m'));
        $venture = $request->query('venture', 'all');

        [$monthStart, $monthEnd] = FinancePnlReport::monthRangeFromQuery($monthInput);

        $prevStart = $monthStart->copy()->subMonthNoOverflow()->startOfMonth();
        $prevEnd = $prevStart->copy()->endOfMonth();

        $revenue = FinancePnlReport::sumForMonth($monthStart, $monthEnd, 'received', $venture);
        $expenses = FinancePnlReport::sumForMonth($monthStart, $monthEnd, 'given', $venture);
        $prevRevenue = FinancePnlReport::sumForMonth($prevStart, $prevEnd, 'received', $venture);
        $prevExpenses = FinancePnlReport::sumForMonth($prevStart, $prevEnd, 'given', $venture);

        $six = FinancePnlReport::sixMonthProfitSeries($venture);

        return ApiJson::ok([
            'month' => $monthStart->format('Y-m'),
            'month_start' => $monthStart->toDateString(),
            'month_end' => $monthEnd->toDateString(),
            'venture' => $venture,
            'ventures' => array_merge(['all'], Finance::VENTURES),
            'summary' => [
                'revenue' => $revenue,
                'expenses' => $expenses,
                'profit' => $revenue - $expenses,
            ],
            'previous_month' => [
                'revenue' => $prevRevenue,
                'expenses' => $prevExpenses,
                'profit' => $prevRevenue - $prevExpenses,
            ],
            'expense_breakdown' => FinancePnlReport::expenseBreakdown($monthStart, $monthEnd, $venture),
            'revenue_by_contact' => FinancePnlReport::revenueByContact($monthStart, $monthEnd, $venture),
            'six_month_trend' => [
                'labels' => $six['labels'],
                'profit' => $six['profit'],
            ],
        ]);
    }

    public function pnl(Request $request)
    {
        $monthInput = $request->query('month', Carbon::now()->format('Y-m'));
        $venture = $request->query('venture', 'all');

        [$monthStart, $monthEnd] = FinancePnlReport::monthRangeFromQuery($monthInput);

        $revenue = FinancePnlReport::sumForMonth($monthStart, $monthEnd, 'received', $venture);
        $expenses = FinancePnlReport::sumForMonth($monthStart, $monthEnd, 'given', $venture);

        return ApiJson::ok([
            'month' => $monthStart->format('Y-m'),
            'venture' => $venture,
            'total_revenue' => $revenue,
            'total_expenses' => $expenses,
            'net_profit' => $revenue - $expenses,
            'revenue_lines' => FinancePnlReport::linesForType($monthStart, $monthEnd, 'received', $venture),
            'expense_lines' => FinancePnlReport::linesForType($monthStart, $monthEnd, 'given', $venture),
        ]);
    }
}
