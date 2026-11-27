<?php

namespace App\Notifications;

class InterviewScheduledNotification extends BaseInterviewNotification
{
    protected function notificationType(): string
    {
        return 'interview_scheduled';
    }
    protected function mailSubject(): string
    {
        return 'Interview scheduled';
    }
    protected function mailIntro(): string
    {
        return 'Your interview has been scheduled.';
    }
    protected function databaseTitle(): string
    {
        return 'Interview scheduled';
    }
    protected function databaseMessage(): string
    {
        return 'You have a new interview scheduled.';
    }
}
