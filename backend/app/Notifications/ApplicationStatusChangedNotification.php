<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class ApplicationStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly \App\Models\Application $application) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $status = Str::headline($this->application->status->value ?? (string) $this->application->status);

        return (new MailMessage)
            ->subject('Your application status has been updated')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('The status of your application has changed.')
            ->line('Position: ' . ($this->application->job?->title ?? 'Job'))
            ->line('New status: ' . $status)
            ->line('Sign in to your account to view the latest application details.');
    }

    public function toArray(object $notifiable): array
    {
        $status = $this->application->status->value ?? (string) $this->application->status;

        return [
            'type' => 'application_status_changed',
            'title' => 'Application status updated',
            'message' => 'Your application for ' . ($this->application->job?->title ?? 'a job') . ' is now ' . Str::headline($status) . '.',
            'application_id' => $this->application->id,
            'job_id' => $this->application->job_id,
            'status' => $status,
        ];
    }
}
