<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Notifications\Messages\MailMessage;

class ApplicationSubmittedNotification extends BaseAppNotification
{
    public function __construct(public readonly Application $application)
    {
        parent::__construct();
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->application->loadMissing(['job.company']);

        $job = $this->application->job;
        $companyName = $job?->company?->name ?? 'the employer';

        return (new MailMessage)
            ->subject('Your job application was submitted')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your application has been submitted successfully.')
            ->line('Position: ' . ($job?->title ?? 'Job'))
            ->line('Company: ' . $companyName)
            ->action('View Application', $this->frontendUrl('/applications/' . $this->application->id))
            ->line('You can sign in to your account at any time to track updates.');
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
