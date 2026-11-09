<?php

namespace App\Models;

use App\Enums\Enums\UserRole;
use App\Enums\Enums\UserStatus;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'status' => UserStatus::class,
        ];
    }

    // Employer → Company (1:1)
    public function company(): HasOne
    {
        return $this->hasOne(Company::class, 'employer_id');
    }

    // Job Seeker → Profile (1:1)
    public function jobSeekerProfile(): HasOne
    {
        return $this->hasOne(JobSeekerProfile::class, 'job_seeker_id');
    }

    // Job Seeker → Skills (1:M)
    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class, 'job_seeker_id');
    }

    // Job Seeker → Educations (1:M)
    public function educations(): HasMany
    {
        return $this->hasMany(Education::class, 'job_seeker_id');
    }

    // Job Seeker → Experiences (1:M)
    public function experiences(): HasMany
    {
        return $this->hasMany(Experience::class, 'job_seeker_id');
    }

    // Job Seeker → Resumes (1:M)
    public function resumes(): HasMany
    {
        return $this->hasMany(Resume::class, 'job_seeker_id');
    }

    // Job Seeker → Applications (1:M)
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'job_seeker_id');
    }

    // Job Seeker → Job Offers (1:M)
    public function jobOffers(): HasMany
    {
        return $this->hasMany(JobOffer::class, 'job_seeker_id');
    }

    // User → AI Analyses (1:M)
    public function aiAnalyses(): HasMany
    {
        return $this->hasMany(AiAnalysis::class, 'user_id');
    }

    // User → AI Recommendations (1:M)
    public function aiRecommendations(): HasMany
    {
        return $this->hasMany(AiRecommendation::class, 'user_id');
    }

    // User → Email Logs (1:M)
    public function emailLogs(): HasMany
    {
        return $this->hasMany(EmailLog::class, 'recipient_id');
    }

    // User → Activity Logs (1:M)
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'actor_id');
    }

    // Admin → Approved Jobs (1:M)
    public function approvedJobs(): HasMany
    {
        return $this->hasMany(Job::class, 'approved_by');
    }

    // User → Saved Jobs (1:M)
    public function savedJobs(): HasMany
    {
        return $this->hasMany(SavedJob::class, 'user_id');
    }
}
