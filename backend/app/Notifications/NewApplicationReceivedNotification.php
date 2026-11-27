<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Notifications\Messages\MailMessage;

class NewApplicationReceivedNotification extends BaseAppNotification
{
    public function __construct(public readonly Application $application)
    {
        parent::__construct();
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->application->loadMissing(['job.company', 'jobSeeker']);

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
            ->action('Review Applicants', $this->frontendUrl('/employer/applicants'))
            ->line('Sign in to your employer account to review the candidate profile and resume.');
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
