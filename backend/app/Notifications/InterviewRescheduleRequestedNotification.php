<?php

namespace App\Notifications;

class InterviewRescheduleRequestedNotification extends BaseInterviewNotification
{
    protected function notificationType(): string
    {
        return 'interview_reschedule_requested';
    }
    protected function mailSubject(): string
    {
        return 'Interview reschedule requested';
    }
    protected function mailIntro(): string
    {
        return 'The candidate has requested a change to the interview schedule.';
    }
    protected function databaseTitle(): string
    {
        return 'Interview reschedule requested';
    }
    protected function databaseMessage(): string
    {
        return 'A candidate requested an interview reschedule.';
    }

    protected function actionUrl(): string
    {
        return $this->frontendUrl('/employer/interviews?interview_id=' . $this->interview->id);
    }
}
