<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\ApiJson;
use App\Models\Task;
use App\Models\Lead;
use App\Models\DailyReport;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MorningBriefApiController extends Controller
{
    /**
     * Aggregated morning brief data for the authenticated admin user.
     * Read-only. Returns today's priorities, overdue items, yesterday's report status.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $today = Carbon::now('Asia/Kolkata')->startOfDay();
        $todayDate = $today->format('Y-m-d');
        $yesterdayDate = $today->copy()->subDay()->format('Y-m-d');

        // 1. Top 5 / daily personal tasks that are still pending
        $priorityTasks = Task::where('created_by', $user->id)
            ->where('category', 'admin_personal')
            ->where('is_active', true)
            ->whereIn('status', ['pending', 'in_progress'])
            ->whereIn('frequency', ['daily', 'top_five'])
            ->orderByRaw("FIELD(priority, 'critical','urgent','high','normal','low')")
            ->get()
            ->map(fn ($t) => [
                'title' => $t->title,
                'priority' => $t->priority,
                'frequency' => $t->frequency,
                'status' => $t->status,
            ])->values()->all();

        // 2. Overdue tasks (due_date before today, not completed)
        $overdueTasks = Task::where('created_by', $user->id)
            ->where('is_active', true)
            ->whereIn('status', ['pending', 'in_progress'])
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', $todayDate)
            ->orderBy('due_date')
            ->get()
            ->map(fn ($t) => [
                'title' => $t->title,
                'priority' => $t->priority,
                'due_date' => $t->due_date?->format('Y-m-d'),
                'days_overdue' => (int) Carbon::parse($t->due_date)->diffInDays($today),
            ])->values()->all();

        // 3. Tasks due today
        $dueTodayTasks = Task::where('created_by', $user->id)
            ->where('is_active', true)
            ->whereIn('status', ['pending', 'in_progress'])
            ->whereDate('due_date', $todayDate)
            ->get()
            ->map(fn ($t) => [
                'title' => $t->title,
                'priority' => $t->priority,
            ])->values()->all();

        // 4. Overdue leads needing follow-up (real schema: business_name, contact_person, stage, next_follow_up)
        $overdueLeads = Lead::where('is_active', true)
            ->whereNotNull('next_follow_up')
            ->whereDate('next_follow_up', '<=', $todayDate)
            ->whereNotIn('stage', ['won', 'lost'])
            ->orderBy('next_follow_up')
            ->limit(15)
            ->get()
            ->map(fn ($l) => [
                'business_name' => $l->business_name,
                'contact_person' => $l->contact_person,
                'stage' => $l->stage,
                'next_follow_up' => $l->next_follow_up ? \Carbon\Carbon::parse($l->next_follow_up)->format('Y-m-d') : null,
                'days_overdue' => $l->next_follow_up ? (int) \Carbon\Carbon::parse($l->next_follow_up)->startOfDay()->diffInDays($today, false) : 0,
            ])->values()->all();

        // 5. Yesterday's daily report status
        $yesterdayReport = DailyReport::where('user_id', $user->id)
            ->where('date', $yesterdayDate)
            ->first();
        $reportStatus = [
            'yesterday_evening_submitted' => $yesterdayReport && $yesterdayReport->evening_submitted_at !== null,
            'today_morning_submitted' => DailyReport::where('user_id', $user->id)
                ->where('date', $todayDate)
                ->whereNotNull('morning_submitted_at')
                ->exists(),
        ];

        return ApiJson::ok([
            'date' => $todayDate,
            'user_name' => $user->name,
            'priority_tasks' => $priorityTasks,
            'due_today' => $dueTodayTasks,
            'overdue_tasks' => $overdueTasks,
            'overdue_leads' => $overdueLeads,
            'report_status' => $reportStatus,
            'counts' => [
                'priority' => count($priorityTasks),
                'due_today' => count($dueTodayTasks),
                'overdue_tasks' => count($overdueTasks),
                'overdue_leads' => count($overdueLeads),
            ],
        ]);
    }
}
