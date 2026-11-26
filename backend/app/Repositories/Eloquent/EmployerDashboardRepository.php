<?php

namespace App\Repositories\Eloquent;

use App\Models\Job;

class EmployerDashboardRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function getJobStats(int $employerId): array
    {
        $jobs = Job::where('employer_id', $employerId);

        return [
            'total' => (clone $jobs)->count(),

            'draft' => (clone $jobs)
                ->where('status', 'draft')
                ->count(),

            'pending' => (clone $jobs)
                ->where('status', 'pending')
                ->count(),

            'published' => (clone $jobs)
                ->where('status', 'published')
                ->count(),

            'rejected' => (clone $jobs)
                ->where('status', 'rejected')
                ->count(),

            'closed' => (clone $jobs)
                ->where('status', 'closed')
                ->count(),
        ];
    }
}
