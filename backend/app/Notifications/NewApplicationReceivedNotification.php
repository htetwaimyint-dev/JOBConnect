<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewApplicationReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly \App\Models\Application $application) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $job = $this->application->job;
        $applicantName = $this->application->jobSeeker?->name
            ?? $this->application->applicant_name
            ?? 'A candidate';

        return (new MailMessage)
            ->subject('You received a new job application')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($applicantName . ' has applied for your job.')
            ->line('Position: ' . ($job?->title ?? 'Job'))
            ->line('Company: ' . ($job?->company?->name ?? 'Your company'))
            ->line('Sign in to your employer account to review the application.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_application_received',
            'title' => 'New application received',
            'message' => ($this->application->jobSeeker?->name ?? $this->application->applicant_name ?? 'A candidate') . ' applied for ' . ($this->application->job?->title ?? 'your job') . '.',
            'application_id' => $this->application->id,
            'job_id' => $this->application->job_id,
        ];
    }
}
