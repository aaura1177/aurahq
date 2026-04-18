<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee; 
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\BirthdayReminderMail; 

class UpcomingBirthdayCheck extends Command
{
    protected $signature = 'check:upcoming-birthdays';
    protected $description = 'Send email for upcoming birthdays (1 day before)';

    public function handle()
    {
        $tomorrow = Carbon::tomorrow();

        $users = Employee::whereMonth('date_of_birth', $tomorrow->month)
            ->whereDay('date_of_birth', $tomorrow->day)
            ->where('status', '1')
            ->get();

        if ($users->count() > 0) {
            Mail::to('office@aurateria.com')->send(new BirthdayReminderMail($users));
            Log::info('Birthday reminder email sent.');
        } else {
            Log::info('No upcoming birthdays for tomorrow.');
        }
    }
}
