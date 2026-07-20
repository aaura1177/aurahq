<?php

namespace App\Http\Controllers;

use App\Models\DailyFocus;
use App\Models\DailyReport;
use App\Models\Finance;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\RevenueTarget;
use App\Models\Task;
use App\Support\RevenueProgress;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();
        $monthStart = $today->copy()->startOfMonth();

        $todayFocus = DailyFocus::query()
            ->where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        $topTasks = Task::query()
            ->where('created_by', $user->id)
            ->where('category', 'admin_personal')
            ->where('is_active', true)
            ->whereIn('status', ['pending', 'in_progress'])
            ->whereIn('frequency', ['daily', 'top_five'])
            ->orderByRaw("CASE priority WHEN 'critical' THEN 0 WHEN 'urgent' THEN 1 ELSE 2 END")
            ->orderBy('id')
            ->limit(5)
            ->get();

        $overdueLeads = Lead::active()
            ->overdue()
            ->orderBy('next_follow_up')
            ->limit(5)
            ->get();

        $monthTarget = RevenueTarget::query()
            ->whereDate('month', $monthStart->format('Y-m-d'))
            ->first();

        $monthlyRevenue = (float) Finance::query()
            ->where('type', 'received')
            ->where('is_active', true)
            ->whereBetween('transaction_date', [$monthStart, $today->copy()->endOfDay()])
            ->sum('amount');

        $pipelineQuery = Lead::active()->whereNotIn('stage', ['won', 'lost']);
        $pipelineCount = (clone $pipelineQuery)->count();
        $pipelineValue = (float) (clone $pipelineQuery)->sum('estimated_value');

        $toChaseCount = Lead::active()->overdue()->count();

        $unpaidQuery = Invoice::query()->whereNotIn('status', ['paid', 'cancelled']);
        $unpaidCount = (clone $unpaidQuery)->count();
        $unpaidTotal = (float) (clone $unpaidQuery)->sum('total_amount');

        $reportStatus = DailyReport::query()
            ->where('user_id', $user->id)
            ->whereDate('date', $today)
            ->whereNotNull('morning_submitted_at')
            ->exists();

        $revMonth = RevenueProgress::currentMonth();
        $revMillion = RevenueProgress::towardMillion();

        return view('dashboard', compact(
            'todayFocus',
            'topTasks',
            'overdueLeads',
            'monthTarget',
            'monthlyRevenue',
            'pipelineCount',
            'pipelineValue',
            'toChaseCount',
            'unpaidCount',
            'unpaidTotal',
            'reportStatus',
            'revMonth',
            'revMillion',
        ));
    }
}
