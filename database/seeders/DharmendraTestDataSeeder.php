<?php

namespace Database\Seeders;

use App\Models\AttendanceRecord;
use App\Models\DailyReport;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DharmendraTestDataSeeder extends Seeder
{
    public function run(): void
    {
        $employeeRole = Role::firstOrCreate(['name' => 'employee']);

        $user = User::firstOrCreate(
            ['email' => 'dharmendra@test.com'],
            [
                'name' => 'Dharmendra',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
        if (!$user->hasRole('employee')) {
            $user->assignRole($employeeRole);
        }

        $today = Carbon::today('Asia/Kolkata');
        $notes = [
            'Completed task review. Follow-up pending with client.',
            'Worked on API integration. Testing in progress.',
            'Morning standup done. Focus on backend today.',
            'Documentation updated. Code review scheduled.',
            'Bug fixes and deployment prep.',
        ];

        for ($i = 1; $i <= 6; $i++) {
            $date = $today->copy()->subDays($i);
            $dateStr = $date->format('Y-m-d');

            AttendanceRecord::firstOrCreate(
                ['user_id' => $user->id, 'date' => $dateStr],
                ['status' => 'present', 'notes' => null]
            );

            $morningAt = $date->copy()->setTime(10, 15, 0)->setTimezone('Asia/Kolkata');
            $eveningAt = $date->copy()->setTime(16, 45, 0)->setTimezone('Asia/Kolkata');

            DailyReport::updateOrCreate(
                ['user_id' => $user->id, 'date' => $dateStr],
                [
                    'morning_submitted_at' => $morningAt,
                    'morning_note' => $notes[$i % count($notes)] . ' (Morning)',
                    'morning_task_ids' => [],
                    'evening_submitted_at' => $eveningAt,
                    'evening_note' => $notes[$i % count($notes)] . ' (Evening)',
                    'evening_task_ids' => [],
                ]
            );
        }

        $this->command?->info('Dharmendra test user and 6 days of attendance + daily reports added.');
    }
}
