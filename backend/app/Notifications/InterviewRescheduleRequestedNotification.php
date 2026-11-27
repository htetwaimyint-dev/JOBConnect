<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class InterviewRescheduleRequestedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly \App\Models\Interview $interview) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $application = $this->interview->application;
        $job = $application?->job;
        $scheduledAt = $this->interview->scheduled_at?->format('M j, Y \a\t g:i A') ?? 'Not specified';

        $mail = (new MailMessage)
            ->subject('Interview reschedule requested')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('The candidate has requested a change to the interview schedule.')
            ->line('Position: ' . ($job?->title ?? 'Job'))
            ->line('Company: ' . ($job?->company?->name ?? 'Company'))
            ->line('Date and time: ' . $scheduledAt)
            ->line('Interview type: ' . Str::headline((string) $this->interview->interview_type));

        if ($this->interview->location) {
            $mail->line('Location: ' . $this->interview->location);
        }

        if ($this->interview->meeting_link) {
            $mail->line('Meeting link: ' . $this->interview->meeting_link);
        }

        return $mail->line('Sign in to your account for the latest interview details.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'interview_reschedule_requested',
            'title' => 'Interview reschedule requested',
            'message' => 'A candidate requested an interview reschedule.',
            'interview_id' => $this->interview->id,
            'application_id' => $this->interview->application_id,
            'scheduled_at' => $this->interview->scheduled_at?->toIso8601String(),
            'status' => $this->interview->status,
        ];
    }
}
