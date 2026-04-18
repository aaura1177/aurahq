<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\DailyReport;
use App\Models\ReportEditGrant;
use App\Models\ReportSubmissionOverride;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DailyReportApiController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $employees = $user->hasRole('super-admin')
            ? User::role('employee')->where('is_active', true)->orderBy('name')->get(['id', 'name', 'email'])
            : collect([$user]);
        $start = $request->filled('start_date') ? Carbon::parse($request->start_date) : Carbon::today('Asia/Kolkata');
        $end = $request->filled('end_date') ? Carbon::parse($request->end_date) : Carbon::today('Asia/Kolkata');
        $employeeId = $request->filled('employee_id') ? (int) $request->employee_id : null;

        $query = DailyReport::with('user')->inDateRange($start, $end)->orderBy('date', 'desc')->orderBy('user_id');
        if ($employeeId) {
            $query->where('user_id', $employeeId);
        }
        if ($user->hasRole('employee') && !$user->hasRole('super-admin')) {
            $query->where('user_id', $user->id);
        }
        $reports = $query->get()->map(function ($r) use ($user) {
            $canEdit = $user->hasRole('super-admin') || $this->employeeCanEditReport($r);
            return [
                'id' => $r->id,
                'user_id' => $r->user_id,
                'user' => $r->user ? ['id' => $r->user->id, 'name' => $r->user->name] : null,
                'date' => $r->date->format('Y-m-d'),
                'morning_submitted_at' => $r->morning_submitted_at?->toIso8601String(),
                'evening_submitted_at' => $r->evening_submitted_at?->toIso8601String(),
                'morning_note' => $r->morning_note,
                'evening_note' => $r->evening_note,
                'morning_task_ids' => $r->morning_task_ids ?? [],
                'evening_task_ids' => $r->evening_task_ids ?? [],
                'can_edit' => $canEdit,
            ];
        });

        return response()->json([
            'data' => $reports,
            'employees' => $employees,
            'start_date' => $start->format('Y-m-d'),
            'end_date' => $end->format('Y-m-d'),
        ]);
    }

    public function show(Request $request, DailyReport $daily_report)
    {
        if ($request->user()->id !== $daily_report->user_id && !$request->user()->hasRole('super-admin')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $daily_report->load('user');
        $canEdit = $request->user()->hasRole('super-admin') || $this->employeeCanEditReport($daily_report);
        $morningTasks = $daily_report->morningTasks()->map(fn ($t) => ['id' => $t->id, 'title' => $t->title, 'note' => $daily_report->getMorningTaskNote($t->id)])->values();
        $eveningTasks = $daily_report->eveningTasks()->map(fn ($t) => ['id' => $t->id, 'title' => $t->title, 'note' => $daily_report->getEveningTaskNote($t->id)])->values();

        return response()->json([
            'data' => [
                'id' => $daily_report->id,
                'user' => $daily_report->user ? ['id' => $daily_report->user->id, 'name' => $daily_report->user->name] : null,
                'date' => $daily_report->date->format('Y-m-d'),
                'morning_submitted_at' => $daily_report->morning_submitted_at?->toIso8601String(),
                'evening_submitted_at' => $daily_report->evening_submitted_at?->toIso8601String(),
                'morning_note' => $daily_report->morning_note,
                'evening_note' => $daily_report->evening_note,
                'morning_tasks' => $morningTasks,
                'evening_tasks' => $eveningTasks,
                'can_edit' => $canEdit,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $isSuperAdmin = $user->hasRole('super-admin');
        if (!$isSuperAdmin && !$user->can('create daily reports')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $ignoreTime = config('daily_reports.ignore_time_window', false);

        $rules = [
            'date' => 'required|date',
            'slot' => 'required|in:morning,evening',
            'note' => 'nullable|string|max:2000',
            'task_ids' => 'nullable|array',
            'task_ids.*' => 'exists:tasks,id',
            'task_notes' => 'nullable|array',
            'task_notes.*' => 'nullable|string|max:1000',
        ];
        if ($isSuperAdmin) {
            $rules['user_id'] = 'required|exists:users,id';
        }
        $request->validate($rules);

        $targetUserId = $isSuperAdmin ? (int) $request->user_id : $user->id;
        if ($isSuperAdmin && !User::find($targetUserId)?->hasRole('employee')) {
            return response()->json(['message' => 'Selected user must be an employee.'], 422);
        }

        $date = Carbon::parse($request->date);
        $now = now()->setTimezone('Asia/Kolkata');
        $slot = $request->slot;
        $overrideMorning = ReportSubmissionOverride::hasMorningOverride($targetUserId, $date->format('Y-m-d'));
        $overrideEvening = ReportSubmissionOverride::hasEveningOverride($targetUserId, $date->format('Y-m-d'));

        if (!$isSuperAdmin && !$ignoreTime) {
            if ($slot === 'morning' && !$overrideMorning && !DailyReport::isWithinMorningWindow($now)) {
                return response()->json(['message' => 'Morning report can only be submitted till 11:00 AM IST.'], 422);
            }
            if ($slot === 'evening') {
                if (!$overrideEvening && !DailyReport::isWithinEveningWindow($now)) {
                    return response()->json(['message' => 'Evening report can only be submitted till 5:15 PM IST.'], 422);
                }
                $presentIds = AttendanceRecord::getPresentEmployeeIdsForDate($date);
                if (!in_array($user->id, $presentIds)) {
                    return response()->json(['message' => 'Evening report is only for days you are marked present.'], 422);
                }
            }
        }

        $report = DailyReport::firstOrCreate(
            ['user_id' => $targetUserId, 'date' => $date->format('Y-m-d')],
            ['morning_task_ids' => [], 'evening_task_ids' => [], 'morning_task_notes' => [], 'evening_task_notes' => []]
        );

        $taskIds = array_values(array_filter(array_map('intval', $request->task_ids ?? [])));
        $taskNotesRaw = $request->task_notes ?? [];
        $taskNotes = [];
        foreach ($taskIds as $id) {
            if (!empty(trim($taskNotesRaw[$id] ?? ''))) {
                $taskNotes[(string) $id] = trim($taskNotesRaw[$id]);
            }
        }

        if ($slot === 'morning') {
            $report->morning_submitted_at = $now;
            $report->morning_note = $request->note;
            $report->morning_task_ids = $taskIds;
            $report->morning_task_notes = $taskNotes;
        } else {
            $report->evening_submitted_at = $now;
            $report->evening_note = $request->note;
            $report->evening_task_ids = $taskIds;
            $report->evening_task_notes = $taskNotes;
        }
        $report->save();

        return response()->json(['message' => 'Report submitted', 'data' => ['id' => $report->id]], 201);
    }

    public function update(Request $request, DailyReport $daily_report)
    {
        if ($request->user()->id !== $daily_report->user_id && !$request->user()->hasRole('super-admin')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        if (!$request->user()->hasRole('super-admin') && !$this->employeeCanEditReport($daily_report)) {
            return response()->json(['message' => 'You can only edit within ' . config('daily_reports.employee_edit_days', 1) . ' day(s) of the report date.'], 403);
        }

        $request->validate([
            'morning_note' => 'nullable|string|max:2000',
            'evening_note' => 'nullable|string|max:2000',
            'morning_task_ids' => 'nullable|array',
            'morning_task_ids.*' => 'exists:tasks,id',
            'evening_task_ids' => 'nullable|array',
            'evening_task_ids.*' => 'exists:tasks,id',
            'morning_task_notes' => 'nullable|array',
            'evening_task_notes' => 'nullable|array',
        ]);

        $now = now()->setTimezone('Asia/Kolkata');
        $isSuperAdmin = $request->user()->hasRole('super-admin');
        $hasEditGrant = !$isSuperAdmin && ReportEditGrant::hasValidGrant($daily_report->user_id, $daily_report->date->format('Y-m-d'));
        $overrideMorning = ReportSubmissionOverride::hasMorningOverride($daily_report->user_id, $daily_report->date->format('Y-m-d'));
        $overrideEvening = ReportSubmissionOverride::hasEveningOverride($daily_report->user_id, $daily_report->date->format('Y-m-d'));
        $ignoreTime = config('daily_reports.ignore_time_window', false);

        if ($request->has('morning_note') || $request->has('morning_task_ids')) {
            if (!$isSuperAdmin && !$ignoreTime && !$hasEditGrant && !$overrideMorning && !DailyReport::isWithinMorningWindow($now)) {
                return response()->json(['message' => 'Morning report can only be edited till 11:00 AM IST.'], 422);
            }
            $daily_report->morning_note = $request->morning_note;
            $daily_report->morning_task_ids = array_values(array_map('intval', $request->morning_task_ids ?? []));
            $mn = $request->morning_task_notes ?? [];
            $notes = [];
            foreach ($daily_report->morning_task_ids as $id) {
                $v = trim($mn[(string)$id] ?? $mn[$id] ?? '');
                if ($v !== '') $notes[(string)$id] = $v;
            }
            $daily_report->morning_task_notes = $notes;
            if (!$daily_report->morning_submitted_at) $daily_report->morning_submitted_at = $now;
        }
        if ($request->has('evening_note') || $request->has('evening_task_ids')) {
            if (!$isSuperAdmin && !$ignoreTime && !$hasEditGrant && !$overrideEvening && !DailyReport::isWithinEveningWindow($now)) {
                return response()->json(['message' => 'Evening report can only be edited till 5:15 PM IST.'], 422);
            }
            $daily_report->evening_note = $request->evening_note;
            $daily_report->evening_task_ids = array_values(array_map('intval', $request->evening_task_ids ?? []));
            $en = $request->evening_task_notes ?? [];
            $notes = [];
            foreach ($daily_report->evening_task_ids as $id) {
                $v = trim($en[(string)$id] ?? $en[$id] ?? '');
                if ($v !== '') $notes[(string)$id] = $v;
            }
            $daily_report->evening_task_notes = $notes;
            if (!$daily_report->evening_submitted_at) $daily_report->evening_submitted_at = $now;
        }
        $daily_report->save();

        return response()->json(['message' => 'Updated', 'data' => ['id' => $daily_report->id]]);
    }

    public function destroy(Request $request, DailyReport $daily_report)
    {
        if (!$request->user()->hasRole('super-admin')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $daily_report->delete();
        return response()->json(['message' => 'Deleted']);
    }

    public function createContext(Request $request)
    {
        $user = $request->user();
        $isSuperAdmin = $user->hasRole('super-admin');
        $employees = $isSuperAdmin ? User::role('employee')->where('is_active', true)->orderBy('name')->get(['id', 'name']) : [];
        $date = $request->filled('date') ? Carbon::parse($request->date) : Carbon::today('Asia/Kolkata');
        $targetUserId = $request->filled('user_id') ? (int) $request->user_id : $user->id;
        $targetUser = User::find($targetUserId) ?? $user;
        $assignedTasks = Task::where('assigned_to', $targetUser->id)->where('is_active', true)->orderBy('title')->get(['id', 'title']);
        $report = DailyReport::firstOrNew(['user_id' => $targetUser->id, 'date' => $date->format('Y-m-d')]);
        $now = now()->setTimezone('Asia/Kolkata');
        return response()->json([
            'employees' => $employees,
            'target_user_id' => $targetUser->id,
            'date' => $date->format('Y-m-d'),
            'assigned_tasks' => $assignedTasks,
            'has_morning' => (bool) $report->morning_submitted_at,
            'has_evening' => (bool) $report->evening_submitted_at,
            'can_submit_morning' => $isSuperAdmin || config('daily_reports.ignore_time_window') || ReportSubmissionOverride::hasMorningOverride($targetUser->id, $date->format('Y-m-d')) || DailyReport::isWithinMorningWindow($now),
            'can_submit_evening' => $isSuperAdmin || config('daily_reports.ignore_time_window') || ReportSubmissionOverride::hasEveningOverride($targetUser->id, $date->format('Y-m-d')) || DailyReport::isWithinEveningWindow($now),
        ]);
    }

    private function employeeCanEditReport(DailyReport $report): bool
    {
        if (auth()->user()->hasRole('super-admin')) {
            return true;
        }
        if (auth()->id() !== $report->user_id) {
            return false;
        }
        $reportDateStr = $report->date->format('Y-m-d');
        if (ReportEditGrant::hasValidGrant($report->user_id, $reportDateStr)) {
            return true;
        }
        $days = (int) config('daily_reports.employee_edit_days', 1);
        $cutoffStr = Carbon::today('Asia/Kolkata')->subDays($days)->format('Y-m-d');
        return $reportDateStr >= $cutoffStr;
    }
}
