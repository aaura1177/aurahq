<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Holiday; 
use App\Models\Employee; 
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\HolidayNotification;
use Illuminate\Support\Facades\Log;



class SendEmailHoliday extends Command
{
    protected $signature = 'holiday:create';
    protected $description = 'Create holidays for the year';


    public function handle()
{
    $date = Carbon::create(null, 1, 1)->format('Y-m-d'); // January 1st

    // Check if the holiday exists with is_active = 1
    $existingHoliday = Holiday::where('date', $date)
        ->where('is_active', 1)
        ->first();

    if (!$existingHoliday) {
        $this->info("No active holiday found on " . $date);
        return;
    }

    $employees = Employee::where('status', '1')->get();

    foreach ($employees as $employee) {
        Mail::to($employee->email)->send(new HolidayNotification(
            $existingHoliday->name,
            $existingHoliday->date,
            $existingHoliday->remark
        ));
    }

    \Log::info("Holiday notification emails sent to all employees.");
}



}
