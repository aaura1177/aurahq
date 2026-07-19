<?php

namespace App\Services;

use App\Models\DailyFocus;
use App\Models\DailyReport;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;

class CommandCenterService
{
    public function morningBrief(User $user): array
    {
        $today = Carbon::now('Asia/Kolkata')->startOfDay();
        $todayDate = $today->format('Y-m-d');
        $yesterdayDate = $today->copy()->subDay()->format('Y-m-d');

        $priorityTasks = Task::query()
            ->where('created_by', $user->id)
            ->where('category', 'admin_personal')
            ->where('is_active', true)
            ->whereIn('status', ['pending', 'in_progress'])
            ->whereIn('frequency', ['daily', 'top_five', 'weekly'])
            ->orderByRaw("CASE priority WHEN 'critical' THEN 0 WHEN 'urgent' THEN 1 WHEN 'normal' THEN 2 ELSE 3 END")
            ->limit(8)
            ->get();

        $overdueTasks = Task::query()
            ->where('created_by', $user->id)
            ->where('is_active', true)
            ->whereIn('status', ['pending', 'in_progress'])
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', $todayDate)
            ->orderBy('due_date')
            ->limit(10)
            ->get();

        $dueTodayTasks = Task::query()
            ->where('created_by', $user->id)
            ->where('is_active', true)
            ->whereIn('status', ['pending', 'in_progress'])
            ->whereDate('due_date', $todayDate)
            ->limit(10)
            ->get();

        $overdueLeads = Lead::query()
            ->where('is_active', true)
            ->whereNotNull('next_follow_up')
            ->whereDate('next_follow_up', '<=', $todayDate)
            ->whereNotIn('stage', ['won', 'lost'])
            ->orderBy('next_follow_up')
            ->limit(10)
            ->get();

        $yesterdayReport = DailyReport::where('user_id', $user->id)
            ->where('date', $yesterdayDate)
            ->first();

        $focus = DailyFocus::where('user_id', $user->id)
            ->whereDate('date', $todayDate)
            ->first();

        $myDaySlots = [];
        if ($focus) {
            foreach ([1, 2, 3] as $s) {
                $title = $focus->{"task_{$s}_title"};
                if ($title || $focus->{"task_{$s}_id"}) {
                    $myDaySlots[] = [
                        'slot' => $s,
                        'title' => $title ?: 'Linked task',
                        'done' => (bool) $focus->{"task_{$s}_completed"},
                    ];
                }
            }
        }

        return [
            'date' => $todayDate,
            'priority_tasks' => $priorityTasks,
            'due_today' => $dueTodayTasks,
            'overdue_tasks' => $overdueTasks,
            'overdue_leads' => $overdueLeads,
            'my_day_slots' => $myDaySlots,
            'my_day_filled' => count($myDaySlots),
            'my_day_completed' => $focus?->completed_count ?? 0,
            'report_status' => [
                'yesterday_evening_submitted' => $yesterdayReport && $yesterdayReport->evening_submitted_at !== null,
                'today_morning_submitted' => DailyReport::where('user_id', $user->id)
                    ->where('date', $todayDate)
                    ->whereNotNull('morning_submitted_at')
                    ->exists(),
            ],
            'counts' => [
                'priority' => $priorityTasks->count(),
                'due_today' => $dueTodayTasks->count(),
                'overdue_tasks' => $overdueTasks->count(),
                'overdue_leads' => $overdueLeads->count(),
            ],
        ];
    }

    /**
     * Actionable inbox items for the header bell.
     *
     * @return list<array{id:string,tone:string,title:string,detail:string,url:string}>
     */
    public function inbox(User $user): array
    {
        $items = [];
        $today = Carbon::now('Asia/Kolkata')->startOfDay();
        $todayDate = $today->format('Y-m-d');

        $overdueTasks = Task::query()
            ->where('created_by', $user->id)
            ->where('is_active', true)
            ->whereIn('status', ['pending', 'in_progress'])
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', $todayDate)
            ->count();

        if ($overdueTasks > 0) {
            $items[] = [
                'id' => 'overdue-tasks',
                'tone' => 'danger',
                'title' => "{$overdueTasks} overdue task".($overdueTasks === 1 ? '' : 's'),
                'detail' => 'Past due date and still open',
                'url' => route('tasks.personal', ['filter' => 'all']),
            ];
        }

        $overdueLeads = Lead::query()
            ->where('is_active', true)
            ->whereNotNull('next_follow_up')
            ->whereDate('next_follow_up', '<=', $todayDate)
            ->whereNotIn('stage', ['won', 'lost'])
            ->count();

        if ($overdueLeads > 0) {
            $items[] = [
                'id' => 'overdue-leads',
                'tone' => 'warning',
                'title' => "{$overdueLeads} follow-up".($overdueLeads === 1 ? '' : 's').' due',
                'detail' => 'Leads needing contact today or earlier',
                'url' => route('leads.overdue'),
            ];
        }

        $pendingInvoices = Invoice::whereIn('status', ['sent', 'overdue'])->count();
        if ($pendingInvoices > 0) {
            $items[] = [
                'id' => 'pending-invoices',
                'tone' => 'warning',
                'title' => "{$pendingInvoices} unpaid invoice".($pendingInvoices === 1 ? '' : 's'),
                'detail' => 'Sent or overdue',
                'url' => route('invoices.index'),
            ];
        }

        if ($user->hasRole('super-admin')) {
            $focus = DailyFocus::where('user_id', $user->id)->whereDate('date', $todayDate)->first();
            $filled = 0;
            if ($focus) {
                foreach ([1, 2, 3] as $s) {
                    if ($focus->{"task_{$s}_title"} || $focus->{"task_{$s}_id"}) {
                        $filled++;
                    }
                }
            }
            if ($filled < 3) {
                $items[] = [
                    'id' => 'my-day',
                    'tone' => 'info',
                    'title' => 'Set your 3 focuses for today',
                    'detail' => $filled === 0 ? 'My Day is empty' : "{$filled}/3 slots filled",
                    'url' => route('daily-focus.today'),
                ];
            }
        }

        return $items;
    }
}
