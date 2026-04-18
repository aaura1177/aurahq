<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceApiController extends Controller
{
    public function index(Request $request)
    {
        $employees = User::role('employee')->where('is_active', true)->orderBy('name')->get(['id', 'name', 'email']);
        $start = $request->filled('start_date') ? Carbon::parse($request->start_date)->startOfDay() : Carbon::today();
        $end = $request->filled('end_date') ? Carbon::parse($request->end_date)->endOfDay() : Carbon::today();
        $employeeId = $request->filled('employee_id') ? (int) $request->employee_id : null;

        $records = [];
        $totalPresent = 0;
        $totalAbsent = 0;
        $totalOff = 0;
        $attendanceGrid = [];
        $allSummaries = [];

        if ($employeeId) {
            $records = AttendanceRecord::where('user_id', $employeeId)->inDateRange($start, $end)->orderBy('date')->get()
                ->map(fn ($r) => ['id' => $r->id, 'user_id' => $r->user_id, 'date' => $r->date->format('Y-m-d'), 'status' => $r->status, 'notes' => $r->notes]);
            for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
                $eff = $this->effectiveStatus($employeeId, $d->copy());
                if ($eff === AttendanceRecord::STATUS_PRESENT) $totalPresent++;
                elseif ($eff === AttendanceRecord::STATUS_ABSENT) $totalAbsent++;
                else $totalOff++;
            }
        } else {
            $datesInRange = [];
            for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
                $datesInRange[] = $d->copy();
            }
            foreach ($employees as $emp) {
                $present = $absent = $off = 0;
                foreach ($datesInRange as $date) {
                    $eff = $this->effectiveStatus($emp->id, $date);
                    $attendanceGrid[$emp->id][$date->format('Y-m-d')] = $eff;
                    if ($eff === AttendanceRecord::STATUS_PRESENT) $present++;
                    elseif ($eff === AttendanceRecord::STATUS_ABSENT) $absent++;
                    else $off++;
                }
                $allSummaries[$emp->id] = ['present' => $present, 'absent' => $absent, 'off' => $off];
            }
        }

        return response()->json([
            'employees' => $employees,
            'records' => $records,
            'employee_id' => $employeeId,
            'start_date' => $start->format('Y-m-d'),
            'end_date' => $end->format('Y-m-d'),
            'total_present' => $totalPresent,
            'total_absent' => $totalAbsent,
            'total_off' => $totalOff,
            'attendance_grid' => $attendanceGrid,
            'all_summaries' => $allSummaries,
        ]);
    }

    public function report(Request $request)
    {
        $employees = User::role('employee')->where('is_active', true)->orderBy('name')->get(['id', 'name', 'email']);
        $start = $request->filled('start_date') ? Carbon::parse($request->start_date)->startOfMonth() : Carbon::now()->startOfMonth();
        $end = $request->filled('end_date') ? Carbon::parse($request->end_date)->endOfMonth() : Carbon::now()->endOfMonth();
        $summaries = [];
        foreach ($employees as $employee) {
            $present = $absent = $off = 0;
            for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
                $eff = $this->effectiveStatus($employee->id, $d->copy());
                if ($eff === AttendanceRecord::STATUS_PRESENT) $present++;
                elseif ($eff === AttendanceRecord::STATUS_ABSENT) $absent++;
                else $off++;
            }
            $summaries[] = ['employee' => $employee, 'present' => $present, 'absent' => $absent, 'off' => $off];
        }
        return response()->json(['employees' => $employees, 'summaries' => $summaries, 'start_date' => $start->format('Y-m-d'), 'end_date' => $end->format('Y-m-d')]);
    }

    public function store(Request $request)
    {
        $employeeIds = User::role('employee')->pluck('id')->toArray();
        $request->validate([
            'user_id' => 'required|exists:users,id|in:' . implode(',', $employeeIds ?: [0]),
            'date' => 'required|date',
            'status' => 'required|in:present,absent,off',
            'notes' => 'nullable|string|max:500',
        ]);
        $r = AttendanceRecord::updateOrCreate(
            ['user_id' => $request->user_id, 'date' => $request->date],
            ['status' => $request->status, 'notes' => $request->notes]
        );
        return response()->json(['message' => 'Saved', 'data' => ['id' => $r->id, 'user_id' => $r->user_id, 'date' => $r->date->format('Y-m-d'), 'status' => $r->status]], 201);
    }

    public function update(Request $request, AttendanceRecord $attendance)
    {
        $request->validate(['status' => 'required|in:present,absent,off', 'notes' => 'nullable|string|max:500']);
        $attendance->update($request->only('status', 'notes'));
        return response()->json(['message' => 'Updated', 'data' => ['id' => $attendance->id, 'status' => $attendance->status]]);
    }

    public function destroy(AttendanceRecord $attendance)
    {
        $attendance->delete();
        return response()->json(['message' => 'Deleted']);
    }

    private function effectiveStatus(int $userId, Carbon $date): string
    {
        $record = AttendanceRecord::where('user_id', $userId)->whereDate('date', $date)->first();
        return $record ? $record->status : AttendanceRecord::defaultStatusForDate($date);
    }
}
