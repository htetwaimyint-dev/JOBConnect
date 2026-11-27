<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class ApplicationWithdrawnNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly \App\Models\Application $application) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('A candidate withdrew their application')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line(($this->application->jobSeeker?->name ?? $this->application->applicant_name ?? 'A candidate') . ' withdrew their application.')
            ->line('Position: ' . ($this->application->job?->title ?? 'Job'))
            ->line('Sign in to your employer account to review your applications.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'application_withdrawn',
            'title' => 'Application withdrawn',
            'message' => ($this->application->jobSeeker?->name ?? $this->application->applicant_name ?? 'A candidate') . ' withdrew their application for ' . ($this->application->job?->title ?? 'your job') . '.',
            'application_id' => $this->application->id,
            'job_id' => $this->application->job_id,
        ];
    }
}
