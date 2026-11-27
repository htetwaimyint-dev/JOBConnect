<?php

namespace App\Notifications;

use App\Models\Interview;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Str;

abstract class BaseInterviewNotification extends BaseAppNotification
{
    public function __construct(public readonly Interview $interview)
    {
        parent::__construct();
    }

    abstract protected function notificationType(): string;

    abstract protected function mailSubject(): string;

    abstract protected function mailIntro(): string;

    abstract protected function databaseTitle(): string;

    abstract protected function databaseMessage(): string;

    protected function actionUrl(): string
    {
        return $this->frontendUrl('/interviews?interview_id=' . $this->interview->id);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->interview->loadMissing(['application.job.company']);

        $job = $this->interview->application?->job;
        $scheduledAt = $this->interview->scheduled_at?->format('M j, Y \a\t g:i A') ?? 'Not specified';

        $mail = (new MailMessage)
            ->subject($this->mailSubject())
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($this->mailIntro())
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

        return $mail
            ->action('View Interview Details', $this->actionUrl())
            ->line('Sign in to your account for the latest interview details.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => $this->notificationType(),
            'title' => $this->databaseTitle(),
            'message' => $this->databaseMessage(),
            'interview_id' => $this->interview->id,
            'application_id' => $this->interview->application_id,
            'scheduled_at' => $this->interview->scheduled_at?->toIso8601String(),
            'status' => $this->interview->status,
        ];
    }
}
