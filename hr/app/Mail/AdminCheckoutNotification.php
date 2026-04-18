<?php

namespace App\Mail;

use App\Models\Attendance;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminCheckoutNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $attendance;

    public function __construct(Attendance $attendance)
    {
        $this->attendance = $attendance;
    }

    public function build()
    {
        return $this->subject('Employee Checkout Notification')
                    ->view('emails.admincheckout');
    }
}
