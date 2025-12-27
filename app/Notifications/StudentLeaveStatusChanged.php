<?php

namespace App\Notifications;

use App\Models\StudentLeave;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class StudentLeaveStatusChanged extends Notification
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
            ->subject('Leave Application ' . $this->leave->status)
            ->greeting('Hello ' . $notifiable->name)
            ->line('Your leave request from ' . $this->leave->from_date . ' to ' . $this->leave->to_date . ' has been ' . strtolower($this->leave->status) . '.')
            ->line('Remarks: ' . ($this->leave->remarks ?? 'No remarks.'))
            ->salutation('Regards, Hostel Management');
    }
}
