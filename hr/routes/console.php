<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
// Schedule::command('app:auto-mark-absent')->dailyAt('10:00:00');


// Schedule::command('attendance:mark-absentees')->dailyAt('17:00:00');
Schedule::command('attendance:mark-absentees')
    ->dailyAt('17:00:00')
    ->when(function () {
        $today = \Carbon\Carbon::today();

        if ($today->isSunday()) {
            return false;
        }

        if ($today->isSaturday()) {
            $dayOfMonth = $today->day;
            $weekOfMonth = ceil($dayOfMonth / 7);

            if (in_array($weekOfMonth, [1, 3, 5])) {
                return false;
            }
        }

        return true;
    });


Schedule::command('attendance:auto-checkout')->dailyAt('17:00:00');
Schedule::command('app:frontend-checkout')->dailyAt('17:30:00');
Schedule::command('task:pending')->dailyAt('17:00:00'); 
Schedule::command('attendance:auto-checkout:night')->dailyAt('23:59');
Schedule::command('leaves:add-monthly')->monthlyOn(15, '00:00');
Schedule::command('holiday:create')->dailyAt('06:00');
Schedule::command('check:upcoming-birthdays')->dailyAt('08:00'); 
Schedule::command('send:birthday-wishes')->dailyAt('00:00');
