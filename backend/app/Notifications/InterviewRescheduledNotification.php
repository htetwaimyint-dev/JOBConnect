<?php

namespace App\Notifications;

class InterviewRescheduledNotification extends BaseInterviewNotification
{
    protected function notificationType(): string
    {
        return 'interview_rescheduled';
    }
    protected function mailSubject(): string
    {
        return 'Interview rescheduled';
    }
    protected function mailIntro(): string
    {
        return 'Your interview schedule has been changed.';
    }
    protected function databaseTitle(): string
    {
        return 'Interview rescheduled';
    }
    protected function databaseMessage(): string
    {
        return 'Your interview has been rescheduled.';
    }
}
