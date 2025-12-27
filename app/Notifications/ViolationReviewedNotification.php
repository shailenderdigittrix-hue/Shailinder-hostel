<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\DisciplinaryViolation;

class ViolationReviewedNotification extends Notification
{
    use Queueable;

    protected $violation;

    /**
     * Create a new notification instance.
     *
     * @param DisciplinaryViolation $violation
     */
    public function __construct(DisciplinaryViolation $violation)
    {
        $this->violation = $violation;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        // You can add other channels like 'database', 'broadcast', etc.
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $status = ucfirst($this->violation->status);
        $studentName = $this->violation->student->user->name ?? 'Student';

        return (new MailMessage)
                    ->subject("Your Violation has been Reviewed")
                    ->greeting("Hello {$studentName},")
                    ->line("Your disciplinary violation has been reviewed.")
                    ->line("Status: {$status}")
                    ->line("Details: " . ($this->violation->review_notes ?? 'No additional notes.'))
                    // ->action('View Violation', url(route('violations.show', $this->violation->id)))
                    ->line('Thank you for your attention.');
    }

    /**
     * Get the array representation of the notification (for database, etc).
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'violation_id' => $this->violation->id,
            'status' => $this->violation->status,
            'review_notes' => $this->violation->review_notes,
        ];
    }
}
