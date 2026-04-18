<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HolidayNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $holidayName;
    public $holidayDate;
    public $remark;

    public function __construct($holidayName, $holidayDate, $remark = null)
    {
        $this->holidayName = $holidayName;
        $this->holidayDate = $holidayDate;
        $this->remark = $remark;
    }

    public function build()
    {
        return $this->subject('Upcoming Holiday Notification')
                    ->view('emails.holiday_notification');
    }
}

