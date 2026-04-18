<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;

class PendingTasksNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $tasks;

    public function __construct(Collection $tasks)
    {
        $this->tasks = $tasks;
    }

    public function build()
    {
        return $this->subject('Pending Tasks Overdue Notification')
                    ->view('emails.pending_tasks_notification');
    }
}
