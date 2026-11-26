<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use App\Models\Job;
use App\Models\Application;
use App\Models\ActivityLog;
use App\Enums\UserRole;
use App\Enums\JobStatus;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        // ==========================================
        // 1. Total Statistics
        // ==========================================

        $totalUsers = User::query()
            ->whereIn('role', [
                UserRole::JOB_SEEKER->value,
                UserRole::EMPLOYER->value,
            ])
            ->count();

        $totalCompanies = Company::count();
        $totalJobs = Job::count();
        $totalApplications = Application::count();


        // ==========================================
        // 2. User Growth - Last 6 Months
        // ==========================================

        $today = Carbon::today();

        $startMonth = $today->copy()
            ->startOfMonth()
            ->subMonths(5);

        $endDate = $today->copy()->endOfDay();

        // Get monthly registration counts grouped by role
        $growthCounts = User::query()
            ->whereIn('role', [
                UserRole::JOB_SEEKER->value,
                UserRole::EMPLOYER->value,
            ])
            ->whereBetween('created_at', [
                $startMonth,
                $endDate,
            ])
            ->selectRaw('
                YEAR(created_at) as year,
                MONTH(created_at) as month,
                role,
                COUNT(*) as total
            ')
            ->groupBy('year', 'month', 'role')
            ->get()
            ->keyBy(function ($user) {
                return $user->year
                    . '-'
                    . $user->month
                    . '|'
                    . $user->role->value;
            });

        // Generate all 6 months, including months with zero registrations
        $userGrowth = collect();

        for ($i = 0; $i < 6; $i++) {
            $month = $startMonth->copy()->addMonths($i);

            $year = $month->year;
            $monthNumber = $month->month;

            $jobSeekers = $growthCounts->get(
                $year . '-' . $monthNumber . '|' . UserRole::JOB_SEEKER->value
            );

            $employers = $growthCounts->get(
                $year . '-' . $monthNumber . '|' . UserRole::EMPLOYER->value
            );

            $userGrowth->push([
                'date' => $month->format('Y-m'),
                'day' => $month->format('M'),
                'job_seekers' => (int) ($jobSeekers->total ?? 0),
                'employers' => (int) ($employers->total ?? 0),
            ]);
        }


        // ==========================================
        // 3. Job Status Statistics
        // ==========================================

        $jobStatusCounts = Job::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        // Include all statuses defined in JobStatus enum
        $jobStatus = collect(JobStatus::cases())
            ->map(function ($status) use ($jobStatusCounts) {
                return [
                    'status' => $status->value,
                    'total' => (int) ($jobStatusCounts[$status->value] ?? 0),
                ];
            })
            ->values();


        // ==========================================
        // 4. Recent Activities
        // ==========================================

        $recentActivities = ActivityLog::query()
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


        // ==========================================
        // 5. Dashboard API Response
        // ==========================================

        return response()->json([
            'success' => true,
            'message' => 'Dashboard data retrieved successfully.',
            'data' => [
                'stats' => [
                    'total_users' => $totalUsers,
                    'total_companies' => $totalCompanies,
                    'total_jobs' => $totalJobs,
                    'total_applications' => $totalApplications,
                ],
                'user_growth' => $userGrowth,
                'job_status' => $jobStatus,
                'recent_activities' => $recentActivities,
            ],
        ]);
    }
}
