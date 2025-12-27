<?php

namespace App\Notifications;

use App\Models\DisciplinaryViolation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ViolationStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $violation;

    /**
     * Create a new notification instance.
     */
    public function __construct(DisciplinaryViolation $violation)
    {
        $this->violation = $violation;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Violation Status Updated')
            ->greeting("Hello " . ($notifiable->name ?? 'Student') . ",")
            ->line("Your disciplinary violation (Type: **{$this->violation->type}**) has been **{$this->violation->status}**.")
            ->line('Review Notes: ' . ($this->violation->review_notes ?? 'N/A'))
            // ->action('View Details', route('violations.show', $this->violation->id))
            ->line('If you have any questions, please contact the warden.');
    }

    /**
     * Get the array representation of the notification (for database/in-app).
     */
    public function toArray(object $notifiable): array
    {
        return [
            'violation_id' => $this->violation->id,
            'status' => $this->violation->status,
            'type' => $this->violation->type,
        ];
    }
}
