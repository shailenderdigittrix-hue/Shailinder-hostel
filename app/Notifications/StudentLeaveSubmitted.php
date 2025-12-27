<?php

namespace App\Notifications;

use App\Models\StudentLeave;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class StudentLeaveSubmitted extends Notification
{
    use Queueable;

    protected $leave;

    public function __construct(StudentLeave $leave)
    {
        $this->leave = $leave;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Leave Application Submitted')
            ->greeting('Hello ' . $notifiable->name)
            ->line('Your leave request from ' . $this->leave->from_date . ' to ' . $this->leave->to_date . ' has been submitted.')
            ->line('Reason: ' . $this->leave->reason)
            ->line('Status: ' . $this->leave->status)
            ->line('You will be notified once it is reviewed.')
            ->salutation('Regards, Hostel Management');
    }
}
