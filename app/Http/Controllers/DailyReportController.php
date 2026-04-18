<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Models\ReportEditGrant;
use App\Models\ReportSubmissionOverride;
use App\Models\Task;
use App\Models\User;
use App\Models\AttendanceRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class DailyReportController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            // index & show: any auth user; controller restricts non–super-admin to own reports
            // create & store: employee needs permission; super-admin can add for any employee
            new Middleware('permission:create daily reports', only: ['create', 'store']),
            new Middleware('auth', only: ['edit', 'update', 'destroy']),
        ];
    }

    public function index(Request $request)
    {
        $employees = User::role('employee')->where('is_active', true)->orderBy('name')->get();
        $start = $request->filled('start_date') ? Carbon::parse($request->start_date) : Carbon::today('Asia/Kolkata');
        $end = $request->filled('end_date') ? Carbon::parse($request->end_date) : Carbon::today('Asia/Kolkata');
        $employeeId = $request->filled('employee_id') ? $request->employee_id : null;

        $query = DailyReport::with('user')->inDateRange($start, $end)->orderBy('date', 'desc')->orderBy('user_id');
        if ($employeeId) {
            $query->where('user_id', $employeeId);
        }
        if (auth()->user()->hasRole('employee') && !auth()->user()->hasRole('super-admin')) {
            $query->where('user_id', auth()->id());
            $employees = collect([auth()->user()]);
            $employeeId = (string) auth()->id();
            $selectedEmployee = auth()->user();
        }
        $reports = $query->get();

        $selectedEmployee = $employeeId ? $employees->firstWhere('id', (int) $employeeId) : null;
        return view('daily-reports.index', compact('reports', 'employees', 'employeeId', 'selectedEmployee', 'start', 'end'));
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        $isSuperAdmin = $user->hasRole('super-admin');
        $ignoreTime = config('daily_reports.ignore_time_window', false);

        $employees = $isSuperAdmin ? User::role('employee')->where('is_active', true)->orderBy('name')->get() : collect();
        $targetUserId = $user->id;
        if ($isSuperAdmin && $request->filled('user_id')) {
            $targetUserId = (int) $request->user_id;
        } elseif ($isSuperAdmin && $employees->isNotEmpty()) {
            $targetUserId = $employees->first()->id;
        }
        $targetUser = User::find($targetUserId) ?? $user;

        $date = $request->filled('date') ? Carbon::parse($request->date) : Carbon::today('Asia/Kolkata');
        $dateStr = $date->format('Y-m-d');
        $slot = $request->get('slot');

        $report = DailyReport::firstOrNew(['user_id' => $targetUser->id, 'date' => $dateStr]);
        $assignedTasks = Task::where('assigned_to', $targetUser->id)->where('is_active', true)->orderBy('title')->get();

        $now = now()->setTimezone('Asia/Kolkata');
        $overrideMorning = ReportSubmissionOverride::hasMorningOverride($targetUser->id, $dateStr);
        $overrideEvening = ReportSubmissionOverride::hasEveningOverride($targetUser->id, $dateStr);

        $canSubmitMorning = $slot === 'morning' && ($isSuperAdmin || $ignoreTime || $overrideMorning || DailyReport::isWithinMorningWindow($now));
        $canSubmitEvening = $slot === 'evening' && ($isSuperAdmin || $ignoreTime || $overrideEvening || DailyReport::isWithinEveningWindow($now));
        $isEmployee = $user->hasRole('employee');

        $hasMorningForDate = (bool) $report->morning_submitted_at;
        $hasEveningForDate = (bool) $report->evening_submitted_at;
        $chooseSlot = $slot !== 'morning' && $slot !== 'evening';

        return view('daily-reports.create', compact('report', 'date', 'slot', 'assignedTasks', 'canSubmitMorning', 'canSubmitEvening', 'isEmployee', 'isSuperAdmin', 'employees', 'targetUser', 'hasMorningForDate', 'hasEveningForDate', 'chooseSlot', 'overrideMorning', 'overrideEvening'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $isSuperAdmin = $user->hasRole('super-admin');
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
            return back()->with('error', 'Selected user must be an employee.')->withInput();
        }

        $date = Carbon::parse($request->date);
        $now = now()->setTimezone('Asia/Kolkata');
        $slot = $request->slot;

        $overrideMorning = ReportSubmissionOverride::hasMorningOverride($targetUserId, $date->format('Y-m-d'));
        $overrideEvening = ReportSubmissionOverride::hasEveningOverride($targetUserId, $date->format('Y-m-d'));

        if (!$isSuperAdmin && !$ignoreTime) {
            if ($slot === 'morning' && !$overrideMorning && !DailyReport::isWithinMorningWindow($now)) {
                return back()->with('error', 'Morning report can only be submitted till 11:00 AM IST.')->withInput();
            }
            if ($slot === 'evening') {
                if (!$overrideEvening && !DailyReport::isWithinEveningWindow($now)) {
                    return back()->with('error', 'Evening report can only be submitted till 5:15 PM IST.')->withInput();
                }
                $presentIds = AttendanceRecord::getPresentEmployeeIdsForDate($date);
                if (!in_array($user->id, $presentIds)) {
                    return back()->with('error', 'Evening report is only for days you are marked present.')->withInput();
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

        return redirect()->route('daily-reports.show', $report)->with('success', ucfirst($slot) . ' report submitted successfully.');
    }

    public function show(DailyReport $dailyReport)
    {
        $this->authorizeView($dailyReport);
        $dailyReport->load('user');
        $canEditReport = $this->employeeCanEditReport($dailyReport);
        return view('daily-reports.show', compact('dailyReport', 'canEditReport'));
    }

    public function edit(Request $request, DailyReport $dailyReport)
    {
        $this->authorizeView($dailyReport);
        if (!auth()->user()->hasRole('super-admin') && !$this->employeeCanEditReport($dailyReport)) {
            abort(403, 'You can only edit a report within ' . config('daily_reports.employee_edit_days', 1) . ' day(s) of the report date.');
        }
        $user = $dailyReport->user;
        $slot = $request->get('slot', 'morning');
        $assignedTasks = Task::where('assigned_to', $user->id)->where('is_active', true)->orderBy('title')->get();
        $now = now()->setTimezone('Asia/Kolkata');
        $ignoreTime = config('daily_reports.ignore_time_window', false);
        $isSuperAdmin = auth()->user()->hasRole('super-admin');
        $hasEditGrant = ReportEditGrant::hasValidGrant($dailyReport->user_id, $dailyReport->date->format('Y-m-d'));
        $overrideMorning = ReportSubmissionOverride::hasMorningOverride($dailyReport->user_id, $dailyReport->date->format('Y-m-d'));
        $overrideEvening = ReportSubmissionOverride::hasEveningOverride($dailyReport->user_id, $dailyReport->date->format('Y-m-d'));
        $canEditMorning = $isSuperAdmin || $ignoreTime || $hasEditGrant || $overrideMorning || (DailyReport::isWithinMorningWindow($now) && auth()->id() == $dailyReport->user_id);
        $canEditEvening = $isSuperAdmin || $ignoreTime || $hasEditGrant || $overrideEvening || (DailyReport::isWithinEveningWindow($now) && auth()->id() == $dailyReport->user_id);
        return view('daily-reports.edit', compact('dailyReport', 'slot', 'assignedTasks', 'canEditMorning', 'canEditEvening'));
    }

    public function update(Request $request, DailyReport $dailyReport)
    {
        $this->authorizeView($dailyReport);
        if (!auth()->user()->hasRole('super-admin') && !$this->employeeCanEditReport($dailyReport)) {
            return back()->with('error', 'You can only edit a report within ' . config('daily_reports.employee_edit_days', 1) . ' day(s) of the report date.');
        }
        $request->validate([
            'slot' => 'required|in:morning,evening,both',
            'morning_note' => 'nullable|string|max:2000',
            'evening_note' => 'nullable|string|max:2000',
            'morning_task_ids' => 'nullable|array',
            'morning_task_ids.*' => 'exists:tasks,id',
            'evening_task_ids' => 'nullable|array',
            'evening_task_ids.*' => 'exists:tasks,id',
            'morning_task_notes' => 'nullable|array',
            'morning_task_notes.*' => 'nullable|string|max:1000',
            'evening_task_notes' => 'nullable|array',
            'evening_task_notes.*' => 'nullable|string|max:1000',
        ]);

        $now = now()->setTimezone('Asia/Kolkata');
        $isSuperAdmin = auth()->user()->hasRole('super-admin');
        $ignoreTime = config('daily_reports.ignore_time_window', false);
        $hasEditGrant = !$isSuperAdmin && ReportEditGrant::hasValidGrant($dailyReport->user_id, $dailyReport->date->format('Y-m-d'));
        $overrideMorning = !$isSuperAdmin ? ReportSubmissionOverride::hasMorningOverride($dailyReport->user_id, $dailyReport->date->format('Y-m-d')) : false;
        $overrideEvening = !$isSuperAdmin ? ReportSubmissionOverride::hasEveningOverride($dailyReport->user_id, $dailyReport->date->format('Y-m-d')) : false;

        if ($request->has('morning_note') || $request->has('morning_task_ids')) {
            if (!$isSuperAdmin && !$ignoreTime && !$hasEditGrant && !$overrideMorning && !DailyReport::isWithinMorningWindow($now)) {
                return back()->with('error', 'Morning report can only be edited till 11:00 AM IST.');
            }
            $dailyReport->morning_note = $request->morning_note;
            $morningIds = array_values(array_map('intval', $request->morning_task_ids ?? []));
            $dailyReport->morning_task_ids = $morningIds;
            $mn = $request->morning_task_notes ?? [];
            $morningNotes = [];
            foreach ($morningIds as $id) {
                $v = trim($mn[(string)$id] ?? $mn[$id] ?? '');
                if ($v !== '') $morningNotes[(string)$id] = $v;
            }
            $dailyReport->morning_task_notes = $morningNotes;
            if (!$dailyReport->morning_submitted_at) {
                $dailyReport->morning_submitted_at = $now;
            }
        }
        if ($request->has('evening_note') || $request->has('evening_task_ids')) {
            if (!$isSuperAdmin && !$ignoreTime && !$hasEditGrant && !$overrideEvening && !DailyReport::isWithinEveningWindow($now)) {
                return back()->with('error', 'Evening report can only be edited till 5:15 PM IST.');
            }
            $dailyReport->evening_note = $request->evening_note;
            $eveningIds = array_values(array_map('intval', $request->evening_task_ids ?? []));
            $dailyReport->evening_task_ids = $eveningIds;
            $en = $request->evening_task_notes ?? [];
            $eveningNotes = [];
            foreach ($eveningIds as $id) {
                $v = trim($en[(string)$id] ?? $en[$id] ?? '');
                if ($v !== '') $eveningNotes[(string)$id] = $v;
            }
            $dailyReport->evening_task_notes = $eveningNotes;
            if (!$dailyReport->evening_submitted_at) {
                $dailyReport->evening_submitted_at = $now;
            }
        }
        $dailyReport->save();

        return redirect()->route('daily-reports.show', $dailyReport)->with('success', 'Report updated.');
    }

    public function destroy(DailyReport $dailyReport)
    {
        if (!auth()->user()->hasRole('super-admin')) {
            abort(403);
        }
        $dailyReport->delete();
        return redirect()->route('daily-reports.index')->with('success', 'Report deleted.');
    }

    private function authorizeView(DailyReport $report): void
    {
        if (auth()->user()->hasRole('super-admin') || auth()->id() === $report->user_id) {
            return;
        }
        abort(403);
    }

    /** Employee can edit only within N days of report date, or if super-admin granted a time-limited edit grant. */
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

    /** Super-admin only: allow an employee to submit morning or evening report at any time for a given date. One slot at a time; can allow both for same day separately. */
    public function allowSubmission(Request $request)
    {
        if (!auth()->user()->hasRole('super-admin')) {
            abort(403);
        }
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'slot' => 'required|in:morning,evening',
        ]);
        $userId = (int) $request->user_id;
        if (!User::find($userId)?->hasRole('employee')) {
            return back()->with('error', 'User must be an employee.');
        }
        $dateStr = Carbon::parse($request->date)->format('Y-m-d');
        $slot = $request->slot;
        $override = ReportSubmissionOverride::firstOrNew(
            ['user_id' => $userId, 'date' => $dateStr],
            ['allow_morning' => false, 'allow_evening' => false]
        );
        if ($slot === 'morning') {
            $override->allow_morning = true;
        } else {
            $override->allow_evening = true;
        }
        $override->save();
        $slotLabel = $slot === 'morning' ? 'Morning' : 'Evening';
        return back()->with('success', "Employee can now submit {$slotLabel} report at any time for {$dateStr}.");
    }

    /** Super-admin only: manage submission overrides and edit grants. */
    public function manage()
    {
        if (!auth()->user()->hasRole('super-admin')) {
            abort(403);
        }
        $employees = User::role('employee')->where('is_active', true)->orderBy('name')->get();
        $submissionOverrides = ReportSubmissionOverride::with('user')->orderBy('date', 'desc')->orderBy('user_id')->get();
        $editGrants = ReportEditGrant::with(['user', 'grantedBy'])->valid()->orderBy('expires_at')->get();
        return view('daily-reports.manage', compact('employees', 'submissionOverrides', 'editGrants'));
    }

    /** Super-admin only: grant an employee permission to edit a report for a specific date for a time period (e.g. 1 hour). */
    public function grantEditAccess(Request $request)
    {
        if (!auth()->user()->hasRole('super-admin')) {
            abort(403);
        }
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'duration_minutes' => 'required|integer|min:1|max:10080', // up to 7 days
        ]);
        $userId = (int) $request->user_id;
        if (!User::find($userId)?->hasRole('employee')) {
            return back()->with('error', 'User must be an employee.');
        }
        $dateStr = Carbon::parse($request->date)->format('Y-m-d');
        $minutes = (int) $request->duration_minutes;
        $expiresAt = now()->addMinutes($minutes);

        $existing = ReportEditGrant::where('user_id', $userId)->where('date', $dateStr)->valid()->first();
        if ($existing) {
            $existing->expires_at = $expiresAt;
            $existing->granted_by = auth()->id();
            $existing->save();
        } else {
            ReportEditGrant::create([
                'user_id' => $userId,
                'date' => $dateStr,
                'expires_at' => $expiresAt,
                'granted_by' => auth()->id(),
            ]);
        }
        $durationLabel = $minutes >= 60 ? ($minutes / 60) . ' hour(s)' : $minutes . ' minute(s)';
        return back()->with('success', "Edit access granted for report on {$dateStr} for {$durationLabel}.");
    }

    /** Super-admin only: revoke an edit grant. */
    public function revokeEditGrant(Request $request)
    {
        if (!auth()->user()->hasRole('super-admin')) {
            abort(403);
        }
        $request->validate(['user_id' => 'required|exists:users,id', 'date' => 'required|date']);
        $deleted = ReportEditGrant::where('user_id', $request->user_id)->where('date', $request->date)->delete();
        return back()->with($deleted ? 'success' : 'error', $deleted ? 'Edit grant revoked.' : 'No active grant found.');
    }

    /** Super-admin only: revoke a submission override (one slot at a time). */
    public function revokeSubmissionOverride(Request $request)
    {
        if (!auth()->user()->hasRole('super-admin')) {
            abort(403);
        }
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'slot' => 'required|in:morning,evening',
        ]);
        $override = ReportSubmissionOverride::where('user_id', $request->user_id)->where('date', $request->date)->first();
        if (!$override) {
            return back()->with('error', 'Override not found.');
        }
        if ($request->slot === 'morning') {
            $override->allow_morning = false;
        } else {
            $override->allow_evening = false;
        }
        $override->save();
        if (!$override->allow_morning && !$override->allow_evening) {
            $override->delete();
        }
        $slotLabel = $request->slot === 'morning' ? 'Morning' : 'Evening';
        return back()->with('success', "{$slotLabel} submission override revoked.");
    }
}
