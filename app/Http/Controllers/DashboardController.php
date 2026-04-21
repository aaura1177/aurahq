<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Client;
use App\Models\DailyReport;
use App\Models\Finance;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\RevenueTarget;
use App\Models\Task;
use App\Models\Venture;
use App\Models\User;
use App\Models\GroceryListItem;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $defaultTarget = (float) config('app.monthly_revenue_target', 200000);

        // Legacy card data (passed for compatibility; super-admin CEO view uses monthly metrics)
        $totalSpending = Finance::where('type', 'given')->where('is_active', true)->sum('amount');
        $totalReceived = Finance::where('type', 'received')->where('is_active', true)->sum('amount');
        $netBalance = $totalReceived - $totalSpending;

        $pendingTasks = Task::where('status', 'pending')->where('is_active', true)->count();
        $activeUsers = User::where('is_active', true)->count();
        $groceryDue = GroceryListItem::where('status', 'pending')->where('is_active', true)->count();

        // Weekly chart (last 7 days)
        $chartLabels = [];
        $expenseData = [];
        $incomeData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $chartLabels[] = $date->format('D');

            $expenseData[] = Finance::whereDate('transaction_date', $date)
                ->where('type', 'given')
                ->where('is_active', true)
                ->sum('amount');

            $incomeData[] = Finance::whereDate('transaction_date', $date)
                ->where('type', 'received')
                ->where('is_active', true)
                ->sum('amount');
        }

        $recentTransactions = Finance::with('contact')
            ->where('is_active', true)
            ->latest('transaction_date')
            ->take(5)
            ->get();

        // CEO metrics (super-admin)
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();
        $lastMonthStart = Carbon::now()->subMonthNoOverflow()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonthNoOverflow()->endOfMonth();

        $monthlyRevenue = Finance::where('type', 'received')
            ->where('is_active', true)
            ->whereBetween('transaction_date', [$currentMonthStart, $currentMonthEnd])
            ->sum('amount');

        $lastMonthRevenue = Finance::where('type', 'received')
            ->where('is_active', true)
            ->whereBetween('transaction_date', [$lastMonthStart, $lastMonthEnd])
            ->sum('amount');

        $revenueChange = $lastMonthRevenue > 0
            ? round((($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : ($monthlyRevenue > 0 ? 100.0 : 0.0);

        $monthlyExpenses = Finance::where('type', 'given')
            ->where('is_active', true)
            ->whereBetween('transaction_date', [$currentMonthStart, $currentMonthEnd])
            ->sum('amount');

        $monthlyProfit = $monthlyRevenue - $monthlyExpenses;

        $revenueTargetRow = RevenueTarget::whereDate('month', $currentMonthStart->format('Y-m-d'))->first();
        $targetAmount = $revenueTargetRow ? (float) $revenueTargetRow->target_amount : $defaultTarget;
        $targetProgress = $targetAmount > 0 ? min(round(($monthlyRevenue / $targetAmount) * 100, 1), 100) : 0;

        $sixMonthLabels = [];
        $sixMonthRevenue = [];
        $sixMonthTarget = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = Carbon::now()->subMonthsNoOverflow($i)->startOfMonth();
            $sixMonthLabels[] = $m->format('M Y');
            $sixMonthRevenue[] = Finance::where('type', 'received')
                ->where('is_active', true)
                ->whereYear('transaction_date', $m->year)
                ->whereMonth('transaction_date', $m->month)
                ->sum('amount');
            $rt = RevenueTarget::whereDate('month', $m->format('Y-m-d'))->first();
            $sixMonthTarget[] = $rt ? (float) $rt->target_amount : $defaultTarget;
        }

        $tasksDueToday = Task::where('is_active', true)
            ->where('status', '!=', 'completed')
            ->whereDate('due_date', Carbon::today())
            ->count();

        $tasksOverdue = Task::where('is_active', true)
            ->where('status', '!=', 'completed')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', Carbon::today())
            ->count();

        // Daily report missing (IST)
        $nowIst = Carbon::now()->setTimezone('Asia/Kolkata');
        $today = $nowIst->format('Y-m-d');
        $morningReportMissing = [];
        $eveningReportMissing = [];
        if (DailyReport::isPastMorningDeadline($nowIst)) {
            $presentIds = AttendanceRecord::getPresentEmployeeIdsForDate($today);
            $reported = DailyReport::whereDate('date', $today)->whereNotNull('morning_submitted_at')->pluck('user_id')->toArray();
            $missingIds = array_diff($presentIds, $reported);
            $morningReportMissing = User::whereIn('id', $missingIds)->orderBy('name')->get();
        }
        if (DailyReport::isPastEveningDeadline($nowIst)) {
            $presentIds = AttendanceRecord::getPresentEmployeeIdsForDate($today);
            $reported = DailyReport::whereDate('date', $today)->whereNotNull('evening_submitted_at')->pluck('user_id')->toArray();
            $missingIds = array_diff($presentIds, $reported);
            $eveningReportMissing = User::whereIn('id', $missingIds)->orderBy('name')->get();
        }

        $pipelineValue = Lead::active()
            ->whereNotIn('stage', ['won', 'lost'])
            ->sum('estimated_value');
        $activeClientsCount = Client::where('is_active', true)->count();
        $pendingInvoicesAmount = Invoice::whereIn('status', ['sent', 'overdue'])->sum('total_amount');

        $dashboardVentures = auth()->user()->hasRole('super-admin')
            ? Venture::with('lastUpdate')->orderBy('name')->get()
            : collect();

        return view('dashboard', compact(
            'totalSpending',
            'totalReceived',
            'netBalance',
            'pendingTasks',
            'activeUsers',
            'groceryDue',
            'chartLabels',
            'expenseData',
            'incomeData',
            'recentTransactions',
            'morningReportMissing',
            'eveningReportMissing',
            'nowIst',
            'monthlyRevenue',
            'lastMonthRevenue',
            'revenueChange',
            'monthlyExpenses',
            'monthlyProfit',
            'targetAmount',
            'targetProgress',
            'sixMonthLabels',
            'sixMonthRevenue',
            'sixMonthTarget',
            'tasksDueToday',
            'tasksOverdue',
            'pipelineValue',
            'activeClientsCount',
            'pendingInvoicesAmount',
            'dashboardVentures',
        ));
    }
}
