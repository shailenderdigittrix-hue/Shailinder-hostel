<?php

namespace App\Notifications;

use App\Mail\GeneralReminderMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class GeneralReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $reminderMessage;
    protected $subjectLine;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $reminderMessage, string $subjectLine = 'General Reminder')
    {
        $this->reminderMessage = $reminderMessage;
        $this->subjectLine = $subjectLine;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['mail', 'database']; // Email and logs
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new GeneralReminderMail($this->reminderMessage, $this->subjectLine))
            ->to($notifiable->email);
    }

    /**
     * Store in database
     */
    public function toDatabase($notifiable)
    {
        return [
            'subject' => $this->subjectLine,
            'message' => $this->reminderMessage,
        ];
    }
}
