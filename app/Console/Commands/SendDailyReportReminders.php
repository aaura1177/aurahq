<?php

namespace App\Console\Commands;

use App\Mail\DailyReportDisciplinaryMail;
use App\Mail\DailyReportDisciplinarySummaryMail;
use App\Mail\DailyReportMissingSummaryMail;
use App\Mail\DailyReportReminderMail;
use App\Models\AttendanceRecord;
use App\Models\DailyReport;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDailyReportReminders extends Command
{
    protected $signature = 'reports:send-reminders {--slot=morning : morning|evening} {--type=reminder : reminder|disciplinary}';

    protected $description = 'Send daily report reminder (10:20 & 17:00 IST) or disciplinary (11:00 & 17:15 IST) emails';

    public function handle(): int
    {
        $slot = $this->option('slot');
        $type = $this->option('type');
        $today = Carbon::today('Asia/Kolkata')->format('Y-m-d');
        $dateLabel = Carbon::today('Asia/Kolkata')->format('d M Y');

        if ($slot === 'morning') {
            $presentIds = AttendanceRecord::getPresentEmployeeIdsForDate($today);
            $reported = DailyReport::whereDate('date', $today)->whereNotNull('morning_submitted_at')->pluck('user_id')->toArray();
            $missingIds = array_values(array_diff($presentIds, $reported));
        } else {
            $presentIds = AttendanceRecord::getPresentEmployeeIdsForDate($today);
            $reported = DailyReport::whereDate('date', $today)->whereNotNull('evening_submitted_at')->pluck('user_id')->toArray();
            $missingIds = array_values(array_diff($presentIds, $reported));
        }

        if (empty($missingIds)) {
            $this->info("No missing {$slot} reports for {$dateLabel} ({$type}).");
            return self::SUCCESS;
        }

        $missingUsers = User::whereIn('id', $missingIds)->get();
        $missingNames = $missingUsers->pluck('name')->toArray();

        foreach ($missingUsers as $user) {
            if ($user->email) {
                if ($type === 'disciplinary') {
                    Mail::to($user->email)->send(new DailyReportDisciplinaryMail($user, $slot, $dateLabel));
                    $this->line("Disciplinary sent to: {$user->name} ({$user->email})");
                } else {
                    Mail::to($user->email)->send(new DailyReportReminderMail($user, $slot, $dateLabel));
                    $this->line("Reminder sent to: {$user->name} ({$user->email})");
                }
            }
        }

        $adminEmails = User::role('super-admin')->pluck('email')->filter()->toArray();
        if (!empty($adminEmails)) {
            if ($type === 'disciplinary') {
                Mail::to($adminEmails)->send(new DailyReportDisciplinarySummaryMail($slot, $dateLabel, $missingNames));
                $this->line('Disciplinary summary sent to admin(s).');
            } else {
                Mail::to($adminEmails)->send(new DailyReportMissingSummaryMail($slot, $dateLabel, $missingNames));
                $this->line('Reminder summary sent to admin(s).');
            }
        }

        $this->info(count($missingIds) . ' ' . $type . '(s) sent.');
        return self::SUCCESS;
    }
}
