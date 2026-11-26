<?php

namespace App\Repositories\Contracts;

interface EmployerDashboardRepositoryInterface
{
    public function getJobStats(int $employerId): array;

    public function getApplicantStats(int $employerId): array;

    public function getDirectOfferStats(int $employerId): array;

    public function getScheduleStats(int $employerId): array;

    public function getUpcomingInterviews(int $employerId): mixed;

}