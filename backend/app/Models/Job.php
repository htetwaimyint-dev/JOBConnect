<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'category_id',
        'title',
        'description',
        'requirements',
        'responsibilities',
        'employment_type',
        'workplace_type',
        'location',
        'salary_min',
        'salary_max',
        'vacancies',
        'deadline',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'salary_min' => 'decimal:2',
            'salary_max' => 'decimal:2',
            'deadline' => 'date',
            'approved_at' => 'datetime',
        ];
    }

    // Job belongs to Company
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    // Job belongs to Category
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    // Job belongs to Admin who approved it
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Job → Applications (1:M)
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'job_id');
    }

    // Job → Job Offers (1:M)
    public function jobOffers(): HasMany
    {
        return $this->hasMany(JobOffer::class, 'job_id');
    }

    // Job → AI Analyses (1:M)
    public function aiAnalyses(): HasMany
    {
        return $this->hasMany(AiAnalysis::class, 'job_id');
    }

    // Job → AI Recommendations (1:M)
    public function aiRecommendations(): HasMany
    {
        return $this->hasMany(AiRecommendation::class, 'job_id');
    }

    // Job → Saved Jobs (1:M)
    public function savedJobs(): HasMany
    {
        return $this->hasMany(SavedJob::class, 'job_id');
    }
}
