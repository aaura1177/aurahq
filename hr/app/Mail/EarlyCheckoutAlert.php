<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Employee;
use Carbon\Carbon;

class EarlyCheckoutAlert extends Mailable
{
    use Queueable, SerializesModels;

    public $employee;
    public $checkOutTime;

    public function __construct(Employee $employee, $checkOutTime)
    {
        $this->employee = $employee;
        $this->checkOutTime = $checkOutTime;
    }

    public function build()
    {
        return $this->subject('Early Checkout Alert')
                    ->view('emails.early_checkout_alert');
    }
}
