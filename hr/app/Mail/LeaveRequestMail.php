<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LeaveRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $leave;

    public function __construct($leave)
    {
        $this->leave = $leave;
    }

    public function build()
    {
        return $this->subject('New Leave Request Submitted')
                    ->view('emails.leave_request')
                    ->with('leave', $this->leave);
    }
}
