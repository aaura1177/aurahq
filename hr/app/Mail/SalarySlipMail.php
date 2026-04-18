<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SalarySlipMail extends Mailable
{
    use Queueable, SerializesModels;

    public $employee;
    public $salaryMonth;
    public $netSalary;

    public function __construct($employee, $salaryMonth, $netSalary)
    {
        $this->employee = $employee;
        $this->salaryMonth = $salaryMonth;
        $this->netSalary = $netSalary;
    }

    public function build()
    {
        return $this->subject('Salary Slip for ' . $this->salaryMonth->format('F Y'))
            ->view('emails.salary_slip');
    }
}
