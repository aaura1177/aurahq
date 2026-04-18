<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailyReportDisciplinarySummaryMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $slot,
        public string $date,
        public array $missingNames
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Disciplinary – ' . count($this->missingNames) . ' employee(s) did not submit ' . ucfirst($this->slot) . ' report (' . $this->date . ')',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.daily-report-disciplinary-summary',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
