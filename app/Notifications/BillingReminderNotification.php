<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\EmailTemplate;

class BillingReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $student;

    public function __construct($student)
    {
        $this->student = $student;
    }

    public function via($notifiable)
    {
        return ['mail', 'database']; // Sends email and stores notification in DB
    }

    public function toMail($notifiable)
    {
        $template = EmailTemplate::where('slug', 'billing_reminder')->first();

        $subject = $template->subject ?? 'Billing Reminder';
        $body = $template ? str_replace(
            ['{student_name}', '{due_amount}'],
            [$this->student->name, $this->student->due_amount],
            $template->body
        ) : 'Please pay your pending bill.';

        return (new MailMessage)
            ->subject($subject)
            ->line(new \Illuminate\Support\HtmlString($body));
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Billing Reminder',
            'message' => 'Your billing is due for October.',
        ];
    }
}

