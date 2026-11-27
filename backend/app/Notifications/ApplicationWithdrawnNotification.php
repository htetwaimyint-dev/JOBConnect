<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Notifications\Messages\MailMessage;

class ApplicationWithdrawnNotification extends BaseAppNotification
{
    public function __construct(public readonly Application $application)
    {
        parent::__construct();
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->application->loadMissing(['job', 'jobSeeker']);

        $candidateName = $this->application->jobSeeker?->name
            ?? $this->application->applicant_name
            ?? 'A candidate';

        return (new MailMessage)
            ->subject('A candidate withdrew their application')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($candidateName . ' withdrew their application.')
            ->line('Position: ' . ($this->application->job?->title ?? 'Job'))
            ->action('View Applicants', $this->frontendUrl('/employer/applicants'))
            ->line('Sign in to your employer account to review your remaining applications.');
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
