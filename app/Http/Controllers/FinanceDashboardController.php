<?php

namespace App\Http\Controllers;

use App\Models\Finance;
use App\Support\FinancePnlReport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class FinanceDashboardController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view finance', only: ['index', 'pnl']),
        ];
    }

    public function index(Request $request)
    {
        $monthInput = $request->query('month', Carbon::now()->format('Y-m'));
        $venture = $request->query('venture', 'all');

        [$monthStart, $monthEnd] = FinancePnlReport::monthRangeFromQuery($monthInput);

        $prevStart = $monthStart->copy()->subMonthNoOverflow()->startOfMonth();
        $prevEnd = $prevStart->copy()->endOfMonth();

        $revenue = FinancePnlReport::sumForMonth($monthStart, $monthEnd, 'received', $venture);
        $expenses = FinancePnlReport::sumForMonth($monthStart, $monthEnd, 'given', $venture);
        $profit = $revenue - $expenses;

        $prevRevenue = FinancePnlReport::sumForMonth($prevStart, $prevEnd, 'received', $venture);
        $prevExpenses = FinancePnlReport::sumForMonth($prevStart, $prevEnd, 'given', $venture);
        $prevProfit = $prevRevenue - $prevExpenses;

        $expenseBreakdown = FinancePnlReport::expenseBreakdown($monthStart, $monthEnd, $venture);
        $revenueByContact = FinancePnlReport::revenueByContact($monthStart, $monthEnd, $venture);

        $six = FinancePnlReport::sixMonthProfitSeries($venture);

        return view('finance.dashboard', [
            'monthStart' => $monthStart,
            'monthEnd' => $monthEnd,
            'monthParam' => $monthStart->format('Y-m'),
            'venture' => $venture,
            'revenue' => $revenue,
            'expenses' => $expenses,
            'profit' => $profit,
            'prevRevenue' => $prevRevenue,
            'prevExpenses' => $prevExpenses,
            'prevProfit' => $prevProfit,
            'expenseBreakdown' => $expenseBreakdown,
            'revenueByContact' => $revenueByContact,
            'sixMonthLabels' => $six['labels'],
            'sixMonthProfit' => $six['profit'],
            'ventures' => Finance::VENTURES,
        ]);
    }

    public function pnl(Request $request)
    {
        $monthInput = $request->query('month', Carbon::now()->format('Y-m'));
        $venture = $request->query('venture', 'all');

        [$monthStart, $monthEnd] = FinancePnlReport::monthRangeFromQuery($monthInput);

        $revenue = FinancePnlReport::sumForMonth($monthStart, $monthEnd, 'received', $venture);
        $expenses = FinancePnlReport::sumForMonth($monthStart, $monthEnd, 'given', $venture);

        $incomeLines = FinancePnlReport::linesForType($monthStart, $monthEnd, 'received', $venture);
        $expenseLines = FinancePnlReport::linesForType($monthStart, $monthEnd, 'given', $venture);

        return view('finance.pnl', [
            'monthStart' => $monthStart,
            'monthEnd' => $monthEnd,
            'monthParam' => $monthStart->format('Y-m'),
            'venture' => $venture,
            'totalRevenue' => $revenue,
            'totalExpenses' => $expenses,
            'netProfit' => $revenue - $expenses,
            'incomeLines' => $incomeLines,
            'expenseLines' => $expenseLines,
            'ventures' => Finance::VENTURES,
        ]);
    }
}
