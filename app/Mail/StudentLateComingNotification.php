<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

class StudentLateComingNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $date;
    public $minutesLate;

    /**
     * Create a new message instance.
     */
    public function __construct($student, $date, $minutesLate)
    {
        $this->student = $student;
        $this->date = $date;
        $this->minutesLate = $minutesLate;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Late Coming Notification',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.student_late',  // blade view you’ll create
            with: [
                'student' => $this->student,
                'date' => $this->date,
                'minutesLate' => $this->minutesLate,
            ]
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
