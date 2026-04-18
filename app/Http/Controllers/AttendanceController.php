<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AttendanceController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('role:super-admin'),
        ];
    }

    public function index(Request $request)
    {
        $employees = User::role('employee')->where('is_active', true)->orderBy('name')->get();
        $start = $request->filled('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::today();
        $end = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::today();
        $employeeId = $request->filled('employee_id') ? $request->employee_id : null;

        $records = collect();
        $totalPresent = 0;
        $totalAbsent = 0;
        $totalOff = 0;
        $attendanceGrid = [];
        $datesInRange = [];
        $allSummaries = [];

        $selectedEmployee = null;
        if ($employeeId) {
            $selectedEmployee = $employees->firstWhere('id', (int) $employeeId);
            $records = AttendanceRecord::where('user_id', $employeeId)
                ->inDateRange($start, $end)
                ->orderBy('date')
                ->get();
            for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
                $effective = $this->effectiveStatus((int) $employeeId, $d->copy());
                if ($effective === AttendanceRecord::STATUS_PRESENT) {
                    $totalPresent++;
                } elseif ($effective === AttendanceRecord::STATUS_ABSENT) {
                    $totalAbsent++;
                } else {
                    $totalOff++;
                }
            }
        } else {
            for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
                $datesInRange[] = $d->copy();
            }
            foreach ($employees as $emp) {
                $present = 0;
                $absent = 0;
                $off = 0;
                foreach ($datesInRange as $date) {
                    $effective = $this->effectiveStatus($emp->id, $date);
                    $attendanceGrid[$emp->id][$date->format('Y-m-d')] = $effective;
                    if ($effective === AttendanceRecord::STATUS_PRESENT) {
                        $present++;
                    } elseif ($effective === AttendanceRecord::STATUS_ABSENT) {
                        $absent++;
                    } else {
                        $off++;
                    }
                }
                $allSummaries[$emp->id] = ['present' => $present, 'absent' => $absent, 'off' => $off];
            }
        }

        return view('attendance.index', compact(
            'employees',
            'records',
            'employeeId',
            'selectedEmployee',
            'start',
            'end',
            'totalPresent',
            'totalAbsent',
            'totalOff',
            'attendanceGrid',
            'datesInRange',
            'allSummaries'
        ));
    }

    public function report(Request $request)
    {
        $employees = User::role('employee')->where('is_active', true)->orderBy('name')->get();
        $start = $request->filled('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::now()->startOfMonth();
        $end = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::now()->endOfMonth();

        $summaries = [];
        foreach ($employees as $employee) {
            $present = 0;
            $absent = 0;
            $off = 0;
            for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
                $effective = $this->effectiveStatus($employee->id, $d->copy());
                if ($effective === AttendanceRecord::STATUS_PRESENT) {
                    $present++;
                } elseif ($effective === AttendanceRecord::STATUS_ABSENT) {
                    $absent++;
                } else {
                    $off++;
                }
            }
            $summaries[] = [
                'employee' => $employee,
                'present' => $present,
                'absent' => $absent,
                'off' => $off,
            ];
        }

        return view('attendance.report', compact('employees', 'summaries', 'start', 'end'));
    }

    public function create(Request $request)
    {
        $employees = User::role('employee')->where('is_active', true)->orderBy('name')->get();
        $date = $request->filled('date') ? Carbon::parse($request->date) : Carbon::today();
        $defaultStatus = AttendanceRecord::defaultStatusForDate($date);
        return view('attendance.create', compact('employees', 'date', 'defaultStatus'));
    }

    public function store(Request $request)
    {
        $employeeIds = User::role('employee')->pluck('id')->toArray();
        $rules = [
            'user_id' => ['required', 'exists:users,id'],
            'date' => 'required|date',
            'status' => 'required|in:present,absent,off',
            'notes' => 'nullable|string|max:500',
        ];
        if (count($employeeIds) > 0) {
            $rules['user_id'][] = 'in:' . implode(',', $employeeIds);
        }
        $request->validate($rules);
        AttendanceRecord::updateOrCreate(
            ['user_id' => $request->user_id, 'date' => $request->date],
            ['status' => $request->status, 'notes' => $request->notes]
        );
        return redirect()->route('attendance.index', [
            'employee_id' => $request->user_id,
            'start_date' => $request->date,
            'end_date' => $request->date,
        ])->with('success', 'Attendance marked successfully.');
    }

    public function show(AttendanceRecord $attendance)
    {
        return view('attendance.show', compact('attendance'));
    }

    public function edit(AttendanceRecord $attendance)
    {
        return view('attendance.edit', compact('attendance'));
    }

    public function update(Request $request, AttendanceRecord $attendance)
    {
        $request->validate([
            'status' => 'required|in:present,absent,off',
            'notes' => 'nullable|string|max:500',
        ]);
        $attendance->update($request->only('status', 'notes'));
        return redirect()->route('attendance.index', [
            'employee_id' => $attendance->user_id,
            'start_date' => $attendance->date->format('Y-m-d'),
            'end_date' => $attendance->date->format('Y-m-d'),
        ])->with('success', 'Attendance updated successfully.');
    }

    public function destroy(AttendanceRecord $attendance)
    {
        $userId = $attendance->user_id;
        $date = $attendance->date->format('Y-m-d');
        $attendance->delete();
        return redirect()->route('attendance.index', [
            'employee_id' => $userId,
            'start_date' => $date,
            'end_date' => $date,
        ])->with('success', 'Attendance record deleted.');
    }

    private function effectiveStatus(int $userId, Carbon $date): string
    {
        $record = AttendanceRecord::where('user_id', $userId)->whereDate('date', $date)->first();
        return $record ? $record->status : AttendanceRecord::defaultStatusForDate($date);
    }
}
