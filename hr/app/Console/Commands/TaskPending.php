<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task; 
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\PendingTasksNotification;
use Illuminate\Support\Facades\Log;

class TaskPending extends Command
{
    protected $signature = 'task:pending'; 
    protected $description = 'Notify about pending tasks older than 5 days';

    public function handle()
    {
        $tasks = Task::with('user')
    ->where('employee_status', '!=', 'completed')
    ->get();


        if ($tasks->count() > 0) {
            Mail::to('office@aurateria.com')->send(new PendingTasksNotification($tasks));
            Log::info('Pending tasks email sent to admin.');
        } else {
            Log::info('No pending tasks older than 5 days found.');
        }
    }
}
    