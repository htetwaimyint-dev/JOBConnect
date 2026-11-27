<?php

namespace App\Services\Admin;

use App\Models\Category;
use App\Models\User;
use App\Models\Company;
use App\Models\Job;
use App\Models\Application;
use App\Models\ActivityLog;
use App\Enums\UserRole;
use App\Enums\JobStatus;
use Carbon\Carbon;

class DashboardService
{
    /**
     * Get all dashboard data.
     */
    public function getDashboardData(): array
    {
        return [
            'stats' => $this->getStatistics(),
            'user_growth' => $this->getUserGrowth(),
            'job_status' => $this->getJobStatus(),
            'jobs_by_category' => $this->getJobsByCategory(),
            'monthly_jobs' => $this->getMonthlyJobs(),
            'recent_activities' => $this->getRecentActivities(),
        ];
    }

    /**
     * Get total platform statistics.
     */
    private function getStatistics(): array
    {
        $totalUsers = User::query()
            ->whereIn('role', [
                UserRole::JOB_SEEKER->value,
                UserRole::EMPLOYER->value,
            ])
            ->count();

        return [
            'total_users' => $totalUsers,
            'total_companies' => Company::count(),
            'total_jobs' => Job::count(),
            'total_applications' => Application::count(),
        ];
    }

    /**
     * Get user growth for the last 6 months.
     */
    // private function getUserGrowth()
    // {
    //     $today = Carbon::today();

    //     $startMonth = $today->copy()
    //         ->startOfMonth()
    //         ->subMonths(5);

    //     $endDate = $today->copy()->endOfDay();

    //     $growthCounts = User::query()
    //         ->whereIn('role', [
    //             UserRole::JOB_SEEKER->value,
    //             UserRole::EMPLOYER->value,
    //         ])
    //         ->whereBetween('created_at', [
    //             $startMonth,
    //             $endDate,
    //         ])
    //         ->selectRaw('
    //             YEAR(created_at) as year,
    //             MONTH(created_at) as month,
    //             role,
    //             COUNT(*) as total
    //         ')
    //         ->groupBy('year', 'month', 'role')
    //         ->get()
    //         ->keyBy(function ($user) {
    //             return $user->year
    //                 . '-'
    //                 . $user->month
    //                 . '|'
    //                 . $user->getRawOriginal('role');
    //         });

    //     $userGrowth = collect();

    //     for ($i = 0; $i < 6; $i++) {
    //         $month = $startMonth->copy()->addMonths($i);

    //         $year = $month->year;
    //         $monthNumber = $month->month;

    //         $jobSeekers = $growthCounts->get(
    //             $year . '-' . $monthNumber . '|'
    //                 . UserRole::JOB_SEEKER->value
    //         );

    //         $employers = $growthCounts->get(
    //             $year . '-' . $monthNumber . '|'
    //                 . UserRole::EMPLOYER->value
    //         );

    //         $userGrowth->push([
    //             'date' => $month->format('Y-m'),
    //             'day' => $month->format('M'),
    //             'job_seekers' => (int) ($jobSeekers->total ?? 0),
    //             'employers' => (int) ($employers->total ?? 0),
    //         ]);
    //     }

    //     return $userGrowth;
    // }


    /**
     * Get user growth for the last 7 days.
     */
    private function getUserGrowth()
    {
        $today = Carbon::today();

        $startDate = $today->copy()->subDays(6);

        $endDate = $today->copy()->endOfDay();

        $growthCounts = User::query()
            ->whereIn('role', [
                UserRole::JOB_SEEKER->value,
                UserRole::EMPLOYER->value,
            ])
            ->whereBetween('created_at', [
                $startDate,
                $endDate,
            ])
            ->selectRaw('
            DATE(created_at) as date,
            role,
            COUNT(*) as total
        ')
            ->groupBy('date', 'role')
            ->get()
            ->keyBy(function ($user) {
                return $user->date
                    . '|'
                    . $user->getRawOriginal('role');
            });

        $userGrowth = collect();

        for ($i = 0; $i < 7; $i++) {
            $date = $startDate->copy()->addDays($i);

            $dateKey = $date->format('Y-m-d');

            $jobSeekers = $growthCounts->get(
                $dateKey . '|' . UserRole::JOB_SEEKER->value
            );

            $employers = $growthCounts->get(
                $dateKey . '|' . UserRole::EMPLOYER->value
            );

            $userGrowth->push([
                'date' => $dateKey,
                'day' => $date->format('D'),
                'job_seekers' => (int) ($jobSeekers->total ?? 0),
                'employers' => (int) ($employers->total ?? 0),
            ]);
        }

        return $userGrowth;
    }


    /**
     * Get job status statistics.
     */
    private function getJobStatus()
    {
        $jobStatusCounts = Job::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return collect(JobStatus::cases())
            ->map(function ($status) use ($jobStatusCounts) {
                return [
                    'status' => $status->value,
                    'total' => (int) (
                        $jobStatusCounts[$status->value] ?? 0
                    ),
                ];
            })
            ->values();
    }

    /**
     * Get job posts for the last 6 months.
     */
    private function getMonthlyJobs()
    {
        $today = Carbon::today();

        $startMonth = $today->copy()
            ->startOfMonth()
            ->subMonths(5);

        $endDate = $today->copy()->endOfDay();

        $jobCounts = Job::query()
            ->whereBetween('created_at', [
                $startMonth,
                $endDate,
            ])
            ->selectRaw('
            YEAR(created_at) as year,
            MONTH(created_at) as month,
            COUNT(*) as total
        ')
            ->groupBy('year', 'month')
            ->get()
            ->keyBy(function ($job) {
                return $job->year . '-' . $job->month;
            });

        $monthlyJobs = collect();

        for ($i = 0; $i < 6; $i++) {
            $month = $startMonth->copy()->addMonths($i);

            $job = $jobCounts->get(
                $month->year . '-' . $month->month
            );

            $monthlyJobs->push([
                'date' => $month->format('Y-m'),
                'month' => $month->format('M'),
                'total' => (int) ($job->total ?? 0),
            ]);
        }

        return $monthlyJobs;
    }

    /**
     * Get total jobs grouped by category.
     */
    private function getJobsByCategory()
    {
        return Category::query()
            ->withCount('jobs')
            ->get()
            ->map(function ($category) {
                return [
                    'category' => $category->name,
                    'total' => (int) $category->jobs_count,
                ];
            })
            ->values();
    }

    /**
     * Get the latest 5 activities.
     */
    private function getRecentActivities()
    {
        return ActivityLog::query()
            ->with('actor:id,name')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'actor' => $activity->actor?->name ?? 'Unknown User',
                    'action' => $activity->action,
                    'description' => $activity->description,
                    'created_at' => $activity->created_at,
                ];
            })
            ->values();
    }
}
