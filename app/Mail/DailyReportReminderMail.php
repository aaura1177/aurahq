<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailyReportReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $slot,
        public string $date
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Daily Report Not Submitted – ' . ucfirst($this->slot) . ' (' . $this->date . ')',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.daily-report-reminder',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
