<?php

namespace App\Mail;

use App\Models\WorkForm;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WorkFromHomeRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     *
     * @param \App\Models\WorkForm $data
     */
    public function __construct(WorkForm $data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New Work From Home Request')
                    ->view('emails.work_from_home')
                    ->with('data', $this->data);
    }
}
