<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Str;

class ApplicationStatusChangedNotification extends BaseAppNotification
{
    public readonly string $statusValue;

    public function __construct(
        public readonly Application $application,
        ?string $statusValue = null,
    ) {
        parent::__construct();
        $this->statusValue = $statusValue
            ?? ($application->status->value ?? (string) $application->status);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->application->loadMissing(['job.company']);

        $statusLabel = Str::headline($this->statusValue);

        return (new MailMessage)
            ->subject('Your application status has been updated')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('The status of your application has changed.')
            ->line('Position: ' . ($this->application->job?->title ?? 'Job'))
            ->line('Company: ' . ($this->application->job?->company?->name ?? 'Company'))
            ->line('New status: ' . $statusLabel)
            ->action('View Application', $this->frontendUrl('/applications/' . $this->application->id))
            ->line('Sign in to your account to view the latest application details.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'application_status_changed',
            'title' => 'Application status updated',
            'message' => 'Your application for ' . ($this->application->job?->title ?? 'a job') . ' is now ' . Str::headline($this->statusValue) . '.',
            'application_id' => $this->application->id,
            'job_id' => $this->application->job_id,
            'status' => $this->statusValue,
        ];
    }
}
