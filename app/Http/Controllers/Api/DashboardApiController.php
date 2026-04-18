<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\DailyReport;
use App\Models\Finance;
use App\Models\GroceryListItem;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardApiController extends Controller
{
    public function index(Request $request)
    {
        $totalSpending = Finance::where('type', 'given')->where('is_active', true)->sum('amount');
        $totalReceived = Finance::where('type', 'received')->where('is_active', true)->sum('amount');
        $netBalance = $totalReceived - $totalSpending;
        $pendingTasks = Task::where('status', 'pending')->where('is_active', true)->count();
        $activeUsers = User::where('is_active', true)->count();
        $groceryDue = GroceryListItem::where('status', 'pending')->where('is_active', true)->count();

        $chartLabels = [];
        $expenseData = [];
        $incomeData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $chartLabels[] = $date->format('D');
            $expenseData[] = Finance::whereDate('transaction_date', $date)->where('type', 'given')->where('is_active', true)->sum('amount');
            $incomeData[] = Finance::whereDate('transaction_date', $date)->where('type', 'received')->where('is_active', true)->sum('amount');
        }

        $recentTransactions = Finance::with('contact')
            ->where('is_active', true)
            ->latest('transaction_date')
            ->take(5)
            ->get()
            ->map(fn ($f) => [
                'id' => $f->id,
                'amount' => $f->amount,
                'type' => $f->type,
                'transaction_date' => $f->transaction_date?->format('Y-m-d'),
                'contact' => $f->contact ? ['id' => $f->contact->id, 'name' => $f->contact->name] : null,
            ]);

        $nowIst = Carbon::now()->setTimezone('Asia/Kolkata');
        $today = $nowIst->format('Y-m-d');
        $morningReportMissing = [];
        $eveningReportMissing = [];
        if ($request->user()->hasRole('super-admin') && DailyReport::isPastMorningDeadline($nowIst)) {
            $presentIds = AttendanceRecord::getPresentEmployeeIdsForDate($today);
            $reported = DailyReport::whereDate('date', $today)->whereNotNull('morning_submitted_at')->pluck('user_id')->toArray();
            $missingIds = array_diff($presentIds, $reported);
            $morningReportMissing = User::whereIn('id', $missingIds)->orderBy('name')->get(['id', 'name', 'email']);
        }
        if ($request->user()->hasRole('super-admin') && DailyReport::isPastEveningDeadline($nowIst)) {
            $presentIds = AttendanceRecord::getPresentEmployeeIdsForDate($today);
            $reported = DailyReport::whereDate('date', $today)->whereNotNull('evening_submitted_at')->pluck('user_id')->toArray();
            $missingIds = array_diff($presentIds, $reported);
            $eveningReportMissing = User::whereIn('id', $missingIds)->orderBy('name')->get(['id', 'name', 'email']);
        }

        return response()->json([
            'total_spending' => $totalSpending,
            'total_received' => $totalReceived,
            'net_balance' => $netBalance,
            'pending_tasks' => $pendingTasks,
            'active_users' => $activeUsers,
            'grocery_due' => $groceryDue,
            'chart' => ['labels' => $chartLabels, 'expense' => $expenseData, 'income' => $incomeData],
            'recent_transactions' => $recentTransactions,
            'morning_report_missing' => $morningReportMissing,
            'evening_report_missing' => $eveningReportMissing,
        ]);
    }
}
