<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class ApplicationSubmittedNotification extends Notification implements ShouldQueue
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
        $companyName = $job?->company?->name ?? 'the employer';

        return (new MailMessage)
            ->subject('Your job application was submitted')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your application has been submitted successfully.')
            ->line('Position: ' . ($job?->title ?? 'Job'))
            ->line('Company: ' . $companyName)
            ->line('You can sign in to your account to review your application.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'application_submitted',
            'title' => 'Application submitted',
            'message' => 'Your application for ' . ($this->application->job?->title ?? 'a job') . ' was submitted successfully.',
            'application_id' => $this->application->id,
            'job_id' => $this->application->job_id,
        ];
    }
}
