<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WrongDeviceAttendence extends Mailable
{
    use Queueable, SerializesModels;

    public $mailMessage;

    public function __construct($mailMessage)
    {
        $this->mailMessage = $mailMessage;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Wrong Device Attendance',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.wrong_device_attendence',
            with: [
                'mailMessage' => $this->mailMessage,
            ],
        );
    }



    
}
