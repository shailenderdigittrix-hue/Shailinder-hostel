<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailyAttendanceReport extends Mailable
{
    use Queueable, SerializesModels;

    public $lateStudents;
    public $absentStudents;

    public function __construct($lateStudents, $absentStudents)
    {
        $this->lateStudents = $lateStudents;
        $this->absentStudents = $absentStudents;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Daily Attendance Report',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.daily_attendance',
        );
    }
}


