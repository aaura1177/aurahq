<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminRequests;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\WorkForm;
use App\Models\LeaveRequest;
use App\Models\Holiday;
use Carbon\CarbonPeriod;
use App\Mail\SalarySlipMail;
use Illuminate\Support\Facades\Mail;
use App\Models\EmployeeSalary;

class EmployeeSalaryController extends Controller
{

    public function index(Request $request)
    {
        $employees = Employee::where('status', '1')
            ->where('department_id', 1)
            ->whereNotNull('salary')
            ->get();

        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $query = EmployeeSalary::with('employee')
            ->whereMonth('salary_month', $month)
            ->whereYear('salary_month', $year);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $employeesalary = $query->orderBy('created_at', 'desc')->get();

        return view('admin.salary.index', compact('employeesalary', 'employees', 'month', 'year'));
    }


    public function add_salary()
    {

        $employees = Employee::where('status', '1')
            ->where('department_id', 1)
            ->whereNotNull('salary')
            ->get();


        return  view('admin.salary.add_salary', compact('employees'));
    }









    // public function create_salary(AdminRequests $request)
    // {
    //     $employeeId = $request->employee_id;
    //     $salaryMonth = $request->salary_month;
    //     $salaryMonthStart = Carbon::parse($salaryMonth)->startOfMonth();
    //     $endOfMonth = $salaryMonthStart->copy()->endOfMonth();
    //     $totaldays = $salaryMonthStart->daysInMonth;

    //     $holidays = Holiday::whereBetween('date', [$salaryMonthStart, $endOfMonth])->get();
    //     $totalHolidays = $holidays->count();

    //     $sundays = 0;
    //     $oddSaturdays = 0;
    //     $totalweekoff = 0;

    //     $period = CarbonPeriod::create($salaryMonthStart, $endOfMonth);
    //     foreach ($period as $date) {
    //         if ($date->isSunday()) $sundays++;

    //         if ($date->isSaturday()) {
    //             $weekOfMonth = intval(ceil($date->day / 7));
    //             if ($weekOfMonth % 2 !== 0) $oddSaturdays++;
    //         }
    //     }
    //     $totalweekoff = $oddSaturdays + $sundays;

    //     if ($employeeId === 'all') {
    //         $employees = Employee::all();

    //         foreach ($employees as $employee) {
    //             $exists = EmployeeSalary::where('employee_id', $employee->id)
    //                 ->where('salary_month', $salaryMonthStart)
    //                 ->exists();
    //             if ($exists) continue;

    //             $workingHours = WorkForm::whereBetween('work_date', [$salaryMonthStart, $endOfMonth])
    //                 ->where('user_id', $employee->id)
    //                 ->where('status', 'approved')
    //                 ->get();

    //             $totalSeconds = 0;
    //             foreach ($workingHours as $work) {
    //                 $parts = explode(':', $work->working_hours);
    //                 $hours = (int) $parts[0];
    //                 $minutes = (int) $parts[1];
    //                 $seconds = (int) $parts[2];
    //                 $totalSeconds += ($hours * 3600) + ($minutes * 60) + $seconds;
    //             }
    //             $totalWorkingTime = gmdate('H:i:s', $totalSeconds);

    //             $totalworkformsalary = 0;
    //             if ($workingHours->count() > 0) {
    //                 $workDayHours = 7;
    //                 $employeeSalaryDay = $employee->salary / $totaldays;
    //                 $employeeSalaryPerSecond = ($employeeSalaryDay / $workDayHours) / 3600;
    //                 $totalworkformsalary = round($totalSeconds * $employeeSalaryPerSecond, 2);
    //             }

    //             $leaves = LeaveRequest::where('employee_id', $employee->id)
    //                 ->whereDate('start_at', '<=', $endOfMonth)
    //                 ->whereDate('end_at', '>=', $salaryMonthStart)
    //                 ->where('status', 'approved')
    //                 ->get();

    //             $totalLeaveDays = 0;
    //             $leavetotalbal = 0;

    //             foreach ($leaves as $leave) {
    //                 $start = Carbon::parse($leave->start_at)->greaterThan($salaryMonthStart) ? Carbon::parse($leave->start_at) : $salaryMonthStart;
    //                 $end = Carbon::parse($leave->end_at)->lessThan($endOfMonth) ? Carbon::parse($leave->end_at) : $endOfMonth;
    //                 $days = $start->diffInDays($end) + 1;
    //                 $totalLeaveDays += $days;
    //             }

    //             if ($totalLeaveDays) {
    //                 $paidLeaveDays = min($totalLeaveDays, $employee->monthly_leave); // सिर्फ monthly leave तक
    //                 $perdaySalary = $employee->salary / $totaldays;
    //                 $leavetotalbal = $perdaySalary * $paidLeaveDays;
    //                 $levaebal = $paidLeaveDays;

    //                 // अगर monthly_leave को घटाना हो:
    //                 $employee->monthly_leave -= $paidLeaveDays;
    //                 $employee->update();
    //             }

    //             $perdaySalary = $employee->salary / $totaldays;
    //             $weekoffsalary = $perdaySalary * $totalweekoff;
    //             $totalHolidayssalary = $perdaySalary * $totalHolidays;

    //             $totalEarnedSalary = Attendance::where('employee_id', $employee->id)
    //                 ->whereMonth('date', $salaryMonthStart->month)
    //                 ->whereYear('date', $salaryMonthStart->year)
    //                 ->where('status', 'Present')
    //                 ->sum('earned_salary');

    //             $totalsalary = $totalEarnedSalary + $weekoffsalary + $leavetotalbal + $totalHolidayssalary + $totalworkformsalary;

    //             EmployeeSalary::create([
    //                 'employee_id' => $employee->id,
    //                 'salary_month' => $salaryMonthStart,
    //                 'net_salary' => round($totalsalary, 2),
    //                 'attendance_salay' => $totalEarnedSalary,
    //                 'workform_salary' => $totalworkformsalary,
    //                 'home_working_hours' => $totalWorkingTime,
    //                 'holiday_salary' => $totalHolidayssalary,
    //                 'leave' => $levaebal,
    //                 'leave_bal' => $leavetotalbal,
    //                 'weekoffsalary' => $weekoffsalary,
    //             ]);

    //             if ($employee->email) {
    //                 Mail::to($employee->email)->send(new SalarySlipMail($employee, $salaryMonthStart, $totalsalary));
    //             }
    //         }

    //         return redirect()->route('admin.salary')->with('success', 'Salaries added for all employees.');
    //     } else {
    //         $exists = EmployeeSalary::where('employee_id', $employeeId)
    //             ->where('salary_month', $salaryMonthStart)
    //             ->exists();

    //         if ($exists) {
    //             return back()->withErrors(['salary_month' => 'Salary for this employee and month already exists.']);
    //         }

    //         $employee = Employee::findOrFail($employeeId);

    //         $workingHours = WorkForm::whereBetween('work_date', [$salaryMonthStart, $endOfMonth])
    //             ->where('user_id', $employeeId)
    //             ->where('status', 'approved')
    //             ->get();

    //         $totalSeconds = 0;
    //         foreach ($workingHours as $work) {
    //             $parts = explode(':', $work->working_hours);
    //             $hours = (int) $parts[0];
    //             $minutes = (int) $parts[1];
    //             $seconds = (int) $parts[2];
    //             $totalSeconds += ($hours * 3600) + ($minutes * 60) + $seconds;
    //         }
    //         $totalWorkingTime = gmdate('H:i:s', $totalSeconds);

    //         $totalworkformsalary = 0;
    //         if ($workingHours->count() > 0) {
    //             $workDayHours = 7;
    //             $employeeSalaryDay = $employee->salary / $totaldays;
    //             $employeeSalaryPerSecond = ($employeeSalaryDay / $workDayHours) / 3600;
    //             $totalworkformsalary = round($totalSeconds * $employeeSalaryPerSecond, 2);
    //         }

    //         $leaves = LeaveRequest::where('employee_id', $employee->id)
    //             ->whereDate('start_at', '<=', $endOfMonth)
    //             ->whereDate('end_at', '>=', $salaryMonthStart)
    //             ->where('status', 'approved')
    //             ->get();

    //         $totalLeaveDays = 0;
    //         $leavetotalbal = 0;

    //         foreach ($leaves as $leave) {
    //             $start = Carbon::parse($leave->start_at)->greaterThan($salaryMonthStart) ? Carbon::parse($leave->start_at) : $salaryMonthStart;
    //             $end = Carbon::parse($leave->end_at)->lessThan($endOfMonth) ? Carbon::parse($leave->end_at) : $endOfMonth;
    //             $days = $start->diffInDays($end) + 1;
    //             $totalLeaveDays += $days;
    //         }

    //         if ($totalLeaveDays) {
    //             $paidLeaveDays = min($totalLeaveDays, $employee->monthly_leave);
    //             $perdaySalary = $employee->salary / $totaldays;
    //             $leavetotalbal = $perdaySalary * $paidLeaveDays;
    //             $levaebal = $paidLeaveDays;

    //             $employee->monthly_leave -= $paidLeaveDays;
    //             $employee->update();
    //         }

    //         $perdaySalary = $employee->salary / $totaldays;
    //         $weekoffsalary = $perdaySalary * $totalweekoff;
    //         $totalHolidayssalary = $perdaySalary * $totalHolidays;

    //         $totalEarnedSalary = Attendance::where('employee_id', $employee->id)
    //             ->whereMonth('date', $salaryMonthStart->month)
    //             ->whereYear('date', $salaryMonthStart->year)
    //             ->where('status', 'Present')
    //             ->sum('earned_salary');

    //         $totalsalary = $totalEarnedSalary + $weekoffsalary + $leavetotalbal + $totalHolidayssalary + $totalworkformsalary;

    //         EmployeeSalary::create([
    //             'employee_id' => $employee->id,
    //             'salary_month' => $salaryMonthStart,
    //             'net_salary' => round($totalsalary, 2),
    //             'attendance_salay' => $totalEarnedSalary,
    //             'workform_salary' => $totalworkformsalary,
    //             'home_working_hours' => $totalWorkingTime,
    //             'holiday_salary' => $totalHolidayssalary,
    //             'leave' => $levaebal,
    //             'leave_bal' => $leavetotalbal,
    //             'weekoffsalary' => $weekoffsalary,
    //         ]);

    //         if ($employee->email) {
    //             Mail::to($employee->email)->send(new SalarySlipMail($employee, $salaryMonthStart, $totalsalary));
    //         }

    //         return redirect()->route('admin.salary')->with('success', 'Salaries added for  employees.');
    //     }
    // }





   public function create_salary(AdminRequests $request)
{
    $employeeId = $request->employee_id;
    $salaryMonth = $request->salary_month;
    $salaryMonthStart = Carbon::parse($salaryMonth)->startOfMonth();
    $endOfMonth = $salaryMonthStart->copy()->endOfMonth();
    $totaldays = $salaryMonthStart->daysInMonth;

    $holidays = Holiday::whereBetween('date', [$salaryMonthStart, $endOfMonth])->get();
    $totalHolidays = $holidays->count();

    $sundays = 0;
    $oddSaturdays = 0;
    $totalweekoff = 0;

    $period = CarbonPeriod::create($salaryMonthStart, $endOfMonth);
    foreach ($period as $date) {
        if ($date->isSunday()) $sundays++;
        if ($date->isSaturday()) {
            $weekOfMonth = intval(ceil($date->day / 7));
            if ($weekOfMonth % 2 !== 0) $oddSaturdays++;
        }
    }
    $totalweekoff = $oddSaturdays + $sundays;

    if ($employeeId === 'all') {
        $employees = Employee::all();

        foreach ($employees as $employee) {
            $exists = EmployeeSalary::where('employee_id', $employee->id)
                ->where('salary_month', $salaryMonthStart)
                ->exists();
            if ($exists) continue;

            // WorkForm salary
            $workingHours = WorkForm::whereBetween('work_date', [$salaryMonthStart, $endOfMonth])
                ->where('user_id', $employee->id)
                ->where('status', 'approved')
                ->get();

            $totalSeconds = 0;
            foreach ($workingHours as $work) {
                $parts = explode(':', $work->working_hours);
                $hours = (int) $parts[0];
                $minutes = (int) $parts[1];
                $seconds = (int) $parts[2];
                $totalSeconds += ($hours * 3600) + ($minutes * 60) + $seconds;
            }
            $totalWorkingTime = gmdate('H:i:s', $totalSeconds);

            $totalworkformsalary = 0;
            if ($workingHours->count() > 0) {
                $workDayHours = 7;
                $employeeSalaryDay = $employee->salary / $totaldays;
                $employeeSalaryPerSecond = ($employeeSalaryDay / $workDayHours) / 3600;
                $totalworkformsalary = round($totalSeconds * $employeeSalaryPerSecond, 2);
            }

            // Leave Calculation
            $leaves = LeaveRequest::where('employee_id', $employee->id)
                ->whereDate('start_at', '<=', $endOfMonth)
                ->whereDate('end_at', '>=', $salaryMonthStart)
                ->where('status', 'approved')
                ->get();

            $totalLeaveDays = 0;
            $leavetotalbal = 0;

            foreach ($leaves as $leave) {
                $start = Carbon::parse($leave->start_at)->greaterThan($salaryMonthStart) ? Carbon::parse($leave->start_at) : $salaryMonthStart;
                $end = Carbon::parse($leave->end_at)->lessThan($endOfMonth) ? Carbon::parse($leave->end_at) : $endOfMonth;
                $days = $start->diffInDays($end) + 1;
                $totalLeaveDays += $days;
            }

            if ($totalLeaveDays) {
                $paidLeaveDays = min($totalLeaveDays, $employee->monthly_leave);
                $perdaySalary = $employee->salary / $totaldays;
                $leavetotalbal = $perdaySalary * $paidLeaveDays;
                $levaebal = $paidLeaveDays;
                $employee->monthly_leave -= $paidLeaveDays;
                $employee->update();
            }

            $perdaySalary = $employee->salary / $totaldays;
            $weekoffsalary = $perdaySalary * $totalweekoff;
            $totalHolidayssalary = $perdaySalary * $totalHolidays;

            // Attendance salary based on working_hours
            $attendances = Attendance::where('employee_id', $employee->id)
                ->whereMonth('date', $salaryMonthStart->month)
                ->whereYear('date', $salaryMonthStart->year)
                ->where('status', 'Present')
                ->get();

            $totalAttendanceSeconds = 0;
            foreach ($attendances as $attendance) {
                $parts = explode(':', $attendance->working_hours);
                $hours = (int) $parts[0];
                $minutes = (int) $parts[1];
                $seconds = (int) $parts[2];
                $totalAttendanceSeconds += ($hours * 3600) + ($minutes * 60) + $seconds;
            }

            $perSecondRate = ($employee->salary / $totaldays) / (7 * 3600);
            $totalEarnedSalary = round($totalAttendanceSeconds * $perSecondRate, 2);

            $totalsalary = $totalEarnedSalary + $weekoffsalary + $leavetotalbal + $totalHolidayssalary + $totalworkformsalary;

            EmployeeSalary::create([
                'employee_id' => $employee->id,
                'salary_month' => $salaryMonthStart,
                'net_salary' => round($totalsalary, 2),
                'attendance_salay' => $totalEarnedSalary,
                'workform_salary' => $totalworkformsalary,
                'home_working_hours' => $totalWorkingTime,
                'holiday_salary' => $totalHolidayssalary,
                'leave' => $levaebal ?? 0,
                'leave_bal' => $leavetotalbal,
                'weekoffsalary' => $weekoffsalary,
            ]);

            if ($employee->email) {
                Mail::to($employee->email)->send(new SalarySlipMail($employee, $salaryMonthStart, $totalsalary));
            }
        }

        return redirect()->route('admin.salary')->with('success', 'Salaries added for all employees.');
    } else {
        $exists = EmployeeSalary::where('employee_id', $employeeId)
            ->where('salary_month', $salaryMonthStart)
            ->exists();

        if ($exists) {
            return back()->withErrors(['salary_month' => 'Salary for this employee and month already exists.']);
        }

        $employee = Employee::findOrFail($employeeId);

        // WorkForm salary
        $workingHours = WorkForm::whereBetween('work_date', [$salaryMonthStart, $endOfMonth])
            ->where('user_id', $employeeId)
            ->where('status', 'approved')
            ->get();

        $totalSeconds = 0;
        foreach ($workingHours as $work) {
            $parts = explode(':', $work->working_hours);
            $hours = (int) $parts[0];
            $minutes = (int) $parts[1];
            $seconds = (int) $parts[2];
            $totalSeconds += ($hours * 3600) + ($minutes * 60) + $seconds;
        }
        $totalWorkingTime = gmdate('H:i:s', $totalSeconds);

        $totalworkformsalary = 0;
        if ($workingHours->count() > 0) {
            $workDayHours = 7;
            $employeeSalaryDay = $employee->salary / $totaldays;
            $employeeSalaryPerSecond = ($employeeSalaryDay / $workDayHours) / 3600;
            $totalworkformsalary = round($totalSeconds * $employeeSalaryPerSecond, 2);
        }

        // Leave
        $leaves = LeaveRequest::where('employee_id', $employee->id)
            ->whereDate('start_at', '<=', $endOfMonth)
            ->whereDate('end_at', '>=', $salaryMonthStart)
            ->where('status', 'approved')
            ->get();

        $totalLeaveDays = 0;
        $leavetotalbal = 0;

        foreach ($leaves as $leave) {
            $start = Carbon::parse($leave->start_at)->greaterThan($salaryMonthStart) ? Carbon::parse($leave->start_at) : $salaryMonthStart;
            $end = Carbon::parse($leave->end_at)->lessThan($endOfMonth) ? Carbon::parse($leave->end_at) : $endOfMonth;
            $days = $start->diffInDays($end) + 1;
            $totalLeaveDays += $days;
        }

        if ($totalLeaveDays) {
            $paidLeaveDays = min($totalLeaveDays, $employee->monthly_leave);
            $perdaySalary = $employee->salary / $totaldays;
            $leavetotalbal = $perdaySalary * $paidLeaveDays;
            $levaebal = $paidLeaveDays;
            $employee->monthly_leave -= $paidLeaveDays;
            $employee->update();
        }

        $perdaySalary = $employee->salary / $totaldays;
        $weekoffsalary = $perdaySalary * $totalweekoff;
        $totalHolidayssalary = $perdaySalary * $totalHolidays;

        // Attendance salary from working_hours
        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereMonth('date', $salaryMonthStart->month)
            ->whereYear('date', $salaryMonthStart->year)
            ->where('status', 'Present')
            ->get();

        $totalAttendanceSeconds = 0;
        foreach ($attendances as $attendance) {
            $parts = explode(':', $attendance->working_hours);
            $hours = (int) $parts[0];
            $minutes = (int) $parts[1];
            $seconds = (int) $parts[2];
            $totalAttendanceSeconds += ($hours * 3600) + ($minutes * 60) + $seconds;
        }

        $perSecondRate = ($employee->salary / $totaldays) / (7 * 3600);
        $totalEarnedSalary = round($totalAttendanceSeconds * $perSecondRate, 2);

        $totalsalary = $totalEarnedSalary + $weekoffsalary + $leavetotalbal + $totalHolidayssalary + $totalworkformsalary;

        EmployeeSalary::create([
            'employee_id' => $employee->id,
            'salary_month' => $salaryMonthStart,
            'net_salary' => round($totalsalary, 2),
            'attendance_salay' => $totalEarnedSalary,
            'workform_salary' => $totalworkformsalary,
            'home_working_hours' => $totalWorkingTime,
            'holiday_salary' => $totalHolidayssalary,
            'leave' => $levaebal ?? 0,
            'leave_bal' => $leavetotalbal,
            'weekoffsalary' => $weekoffsalary,
        ]);

        if ($employee->email) {
            Mail::to($employee->email)->send(new SalarySlipMail($employee, $salaryMonthStart, $totalsalary));
        }

        return redirect()->route('admin.salary')->with('success', 'Salaries added for employee.');
    }
}






    public function destroy($id)
    {
        $EmployeeSalary = EmployeeSalary::find($id);

        if (!$EmployeeSalary) {
            return redirect()->route('admin.salary')
                ->with('error', 'Salary not found.');
        }

        $employee = Employee::find($EmployeeSalary->employee_id);
        if ($employee) {
            $employee->monthly_leave += $EmployeeSalary->leave;
            $employee->save();
        }

        $EmployeeSalary->delete();

        return redirect()->route('admin.salary')
            ->with('success', 'Salary deleted successfully.');
    }
}
