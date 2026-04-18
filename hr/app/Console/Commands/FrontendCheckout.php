<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class FrontendCheckout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:frontend-checkout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $attendances = Attendance::whereDate('date', Carbon::today())
            ->whereNull('check_out_time')
            ->where('status', '!=', 'Absent')
            ->get();

        $now = Carbon::now();
        $checkoutDeadline = Carbon::today()->setTime(17, 30, 0);


        if ($now->gt($checkoutDeadline)) {
            $combinedMessage = "Auto Checkout Alert\n\n";

            foreach ($attendances as $attendance) {



                $checkOutTime = $checkoutDeadline;
                $checkInTime = Carbon::parse($attendance->check_in_time);

                $workingSeconds = $checkInTime->diffInSeconds($checkOutTime);
                $workingHours = gmdate('H:i:s', $workingSeconds);
                $overtimeSeconds = max(0, $workingSeconds - (7 * 3600));
                $overtimeHours = gmdate('H:i:s', $overtimeSeconds);

                $employee = Employee::find($attendance->employee_id);
                $employeeName = $employee ? $employee->name : 'Unknown Employee';

                $earnedSalary = 0;
                if ($employee && is_numeric($employee->salary) && $employee->salary > 0) {
                    $daysInMonth = Carbon::now()->daysInMonth;
                    $workDayHours = 7;

                    $employeeSalaryDay = $employee->salary / $daysInMonth;
                    $employeeSalaryPerHour = $employeeSalaryDay / $workDayHours;
                    $employeeSalaryPerMinute = $employeeSalaryPerHour / 60;
                    $employeeSalaryPerSecond = $employeeSalaryPerMinute / 60;

                    $earnedSalary = round($workingSeconds * $employeeSalaryPerSecond, 2);
                }

                $attendance->update([
                    'check_out_time' => $checkOutTime->toTimeString(),
                    'working_hours' => $workingHours,
                    'overtime_hours' => $overtimeHours,
                    'earned_salary' => $earnedSalary,
                ]);

                Log::info("Employee $employeeName (ID: {$attendance->employee_id}) checked out automatically at 17:00");

                $combinedMessage .= <<<EOT
Employee Name: $employeeName
Employee ID: {$attendance->employee_id}
Checkout Time: {$checkOutTime->toTimeString()}
Working Hours: $workingHours
Overtime Hours: $overtimeHours
Earned Salary: ₹$earnedSalary

------------------------------

EOT;
            }

            // Send combined email if any attendance was updated
            if ($attendances->count()) {
                $combinedMessage .= "\nThis is an automated notification.";
                Mail::raw($combinedMessage, function ($message) {
                    $message->to('office@aurateria.com')
                        ->subject('Auto Checkout Summary');
                });
            }
            // office@aurateria.com
            Log::info('Auto checkout process completed.');
        } else {
            Log::info('It is not past 17:00 yet. No auto checkout performed.');
        }
    }
}
