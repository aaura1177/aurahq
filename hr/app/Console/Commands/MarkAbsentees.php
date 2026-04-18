<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\WorkForm;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;



class MarkAbsentees extends Command
{
    protected $signature = 'attendance:mark-absentees';
    protected $description = 'Mark employees as absent who did not check in today';

 public function handle()
{
    $today = Carbon::today()->toDateString();

    // Check if today is a holiday
    $isHoliday = Holiday::whereDate('date', $today)->exists();

$allEmployees = Employee::where('status', 1)->get();

    foreach ($allEmployees as $employee) {
        $checkinExists = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->exists();

        $workform = WorkForm::where('user_id', $employee->id)
            ->whereDate('work_date', $today)
            ->first();

        $work_from = $workform ? 1 : 0;

        if (!$checkinExists) {
            Attendance::create([
                'employee_id' => $employee->id,
                'date'        => $today,
                'shift'       => 'Morning',
                'status'      => $isHoliday ? 'Holiday' : 'Absent',
                'work_from'   => $work_from,
            ]);
        }
    }

    Log::info('Attendance marked successfully (Absent/Holiday).');
}

}
