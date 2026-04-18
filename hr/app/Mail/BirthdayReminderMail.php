<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BirthdayReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $users;

    public function __construct($users)
    {
        $this->users = $users;
    }

    public function build()
    {
        return $this->subject('Birthday Reminder - Tomorrow')
                    ->view('emails.birthday_reminder');
    }
}
