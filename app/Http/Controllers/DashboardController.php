<?php
namespace App\Http\Controllers;
use App\Models\AttendanceRecord;
use App\Models\DailyReport;
use App\Models\Finance;
use App\Models\Task;
use App\Models\User;
use App\Models\GroceryListItem;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Cards Data (Only Active Records)
        $totalSpending = Finance::where('type', 'given')->where('is_active', true)->sum('amount');
        $totalReceived = Finance::where('type', 'received')->where('is_active', true)->sum('amount');
        $netBalance = $totalReceived - $totalSpending;
        
        $pendingTasks = Task::where('status', 'pending')->where('is_active', true)->count();
        $activeUsers = User::where('is_active', true)->count();
        $groceryDue = GroceryListItem::where('status', 'pending')->where('is_active', true)->count();

        // 2. Chart Data (Last 7 Days)
        $chartLabels = [];
        $expenseData = [];
        $incomeData = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $chartLabels[] = $date->format('D'); // Mon, Tue...
            
            $expenseData[] = Finance::whereDate('transaction_date', $date)
                                ->where('type', 'given')
                                ->where('is_active', true)
                                ->sum('amount');
                                
            $incomeData[] = Finance::whereDate('transaction_date', $date)
                                ->where('type', 'received')
                                ->where('is_active', true)
                                ->sum('amount');
        }

        // 3. Recent Activity
        $recentTransactions = Finance::with('contact')
                                ->where('is_active', true)
                                ->latest('transaction_date')
                                ->take(5)
                                ->get();

        // 4. Daily report missing (IST) – show after 11:00 AM and 5:15 PM
        $nowIst = Carbon::now()->setTimezone('Asia/Kolkata');
        $today = $nowIst->format('Y-m-d');
        $morningReportMissing = [];
        $eveningReportMissing = [];
        if (\App\Models\DailyReport::isPastMorningDeadline($nowIst)) {
            $presentIds = AttendanceRecord::getPresentEmployeeIdsForDate($today);
            $reported = DailyReport::whereDate('date', $today)->whereNotNull('morning_submitted_at')->pluck('user_id')->toArray();
            $missingIds = array_diff($presentIds, $reported);
            $morningReportMissing = User::whereIn('id', $missingIds)->orderBy('name')->get();
        }
        if (\App\Models\DailyReport::isPastEveningDeadline($nowIst)) {
            $presentIds = AttendanceRecord::getPresentEmployeeIdsForDate($today);
            $reported = DailyReport::whereDate('date', $today)->whereNotNull('evening_submitted_at')->pluck('user_id')->toArray();
            $missingIds = array_diff($presentIds, $reported);
            $eveningReportMissing = User::whereIn('id', $missingIds)->orderBy('name')->get();
        }

        return view('dashboard', compact(
            'totalSpending', 'totalReceived', 'netBalance',
            'pendingTasks', 'activeUsers', 'groceryDue',
            'chartLabels', 'expenseData', 'incomeData',
            'recentTransactions',
            'morningReportMissing', 'eveningReportMissing', 'nowIst'
        ));
    }
}
