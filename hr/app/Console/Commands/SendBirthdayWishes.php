<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmployeeBirthdayWishMail;
use Illuminate\Support\Facades\Log;


class SendBirthdayWishes extends Command
{
    protected $signature = 'send:birthday-wishes';
    protected $description = 'Send birthday wishes to employees on their birthday';

  public function handle()
{
    $today = Carbon::today();

    $employees = Employee::whereMonth('date_of_birth', $today->month)
        ->whereDay('date_of_birth', $today->day)
        ->where('status', '1')
        ->get();

    Log::info("Found " . $employees->count() . " employees with birthday today.");

    foreach ($employees as $employee) {
        if ($employee->email) {
            Mail::to($employee->email)->send(new EmployeeBirthdayWishMail($employee));
            Log::info("Birthday mail sent to " . $employee->email);
        } else {
            Log::warning("Employee {$employee->id} has no email.");
        }
    }

    Log::info('Birthday wishes process completed.');
}
}
