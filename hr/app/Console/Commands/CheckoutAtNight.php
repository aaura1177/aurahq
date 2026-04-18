<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckoutAtNight extends Command
{
    protected $signature = 'attendance:auto-checkout:night';
    protected $description = 'Automatically checkout employees who didn\'t checkout by 23:59:00';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $attendances = Attendance::whereDate('date', Carbon::today())
            ->whereNull('check_out_time')
            ->where('status', '!=', 'Absent')
            ->get();

        foreach ($attendances as $attendance) {
            if (Carbon::now()->gte(Carbon::today()->setTime(23, 59, 0))) {
                $checkOutTime = Carbon::today()->setTime(23, 59, 0);
                $checkInTime = Carbon::parse($attendance->check_in_time);

                $workingSeconds = $checkInTime->diffInSeconds($checkOutTime);
                $workingHours = gmdate('H:i:s', $workingSeconds);
                $overtimeSeconds = max(0, $workingSeconds - (7 * 3600));
                $overtimeHours = gmdate('H:i:s', $overtimeSeconds);

                $earnedSalary = 0;
                $employee = \App\Models\Employee::find($attendance->employee_id);

                if ($employee && is_numeric($employee->salary) && $employee->salary > 0) {
                    $daysInMonth =  Carbon::now()->daysInMonth;
                    $workDayHours = 7;

                    $salaryPerDay = $employee->salary / $daysInMonth;
                    $salaryPerHour = $salaryPerDay / $workDayHours;
                    $salaryPerMinute = $salaryPerHour / 60;
                    $salaryPerSecond = $salaryPerMinute / 60;

                    $earnedSalary = round($workingSeconds * $salaryPerSecond, 2);
                }

                $attendance->update([
                    'check_out_time'  => $checkOutTime->toTimeString(),
                    'working_hours'   => $workingHours,
                    'overtime_hours'  => $overtimeHours,
                    'earned_salary'   => $earnedSalary,
                ]);

                Log::info("Employee {$attendance->employee_id} checked out automatically at 23:59:00 with ₹$earnedSalary salary");
                $this->info("Employee {$attendance->employee_id} checked out automatically at 23:59:00 with ₹$earnedSalary salary");
            }
        }

        Log::info('Night auto checkout process completed.');
        $this->info('Night auto checkout process completed.');
    }
}
