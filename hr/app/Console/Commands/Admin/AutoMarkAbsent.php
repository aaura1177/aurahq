<?php

namespace App\Console\Commands\Admin;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AutoMarkAbsent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:auto-mark-absent';

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

        $today = Carbon::today()->toDateString();
        $dayOfWeek = Carbon::today()->format('l');

        $isHoliday = Holiday::where('date', $today)->where('is_active', 1)->exists();
        $employees = Employee::where('status', '1')->get();

        if ($dayOfWeek === 'Sunday' || $isHoliday) {
            foreach ($employees as $employee) {

                Attendance::updateOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'date'        => $today,
                    ],
                    [
                        'shift'        => 'Morning',
                        'check_in_time' => '10:00:00',
                        'check_out_time' => '17:00:00',
                        'status'       => 'Present',
                        'working_hours' => '07:00:00',
                    ]
                );
            }
            Log::info("Today is a holiday or Sunday. Cron job skipped.");
            return "Today is Leave";
        }


        foreach ($employees as $employee) {
            $attendanceExists = Attendance::where('employee_id', $employee->id)
                ->whereDate('date', $today)
                ->exists();
            if (!$attendanceExists) {
                Attendance::create([
                    'employee_id'   => $employee->id,
                    'date'          => $today,
                    'shift'         => 'Morning',
                    'check_in_time' => Carbon::now()->toTimeString(),
                    'status'        => 'Absent',

                ]);
            }
        }

        Log::info("Absent status marked for all employees who didn't check-in today.");
    }
}
