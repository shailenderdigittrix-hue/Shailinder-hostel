<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GeneralReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reminderMessage;
    public $subjectLine;

    /**
     * Create a new message instance.
     */
    public function __construct(string $reminderMessage, string $subjectLine = 'General Reminder')
    {
        $this->reminderMessage = $reminderMessage;
        $this->subjectLine = $subjectLine;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectLine,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.general-reminder', // update this to match your actual view file
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
