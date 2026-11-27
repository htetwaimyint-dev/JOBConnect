<?php

namespace App\Notifications;

class InterviewCancelledNotification extends BaseInterviewNotification
{
    protected function notificationType(): string
    {
        return 'interview_cancelled';
    }
    protected function mailSubject(): string
    {
        return 'Interview cancelled';
    }
    protected function mailIntro(): string
    {
        return 'Your interview has been cancelled.';
    }
    protected function databaseTitle(): string
    {
        return 'Interview cancelled';
    }
    protected function databaseMessage(): string
    {
        return 'Your interview has been cancelled.';
    }
}
