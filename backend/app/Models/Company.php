<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'employer_id',
        'name',
        'logo',
        'cover_image',
        'tagline',
        'founded_year',
        'employee_count',
        'gallery',
        'description',
        'industry',
        'website',
        'company_email',
        'phone',
        'address',
        'city',
        'verification_status',
        'verified_at',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
            'founded_year' => 'integer',
            'gallery' => 'array',
        ];
    }

    // Company belongs to Employer (User)
    public function employer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    // Company has many Jobs
    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class, 'company_id');
    }

    public function applicationStatusHistories(): HasMany
    {
        return $this->hasMany(
            ApplicationStatusHistory::class,
            'changed_by_company_id'
        );
    }

    public function jobOffers(): HasMany
    {
        return $this->hasMany(JobOffer::class, 'company_id');
    }
}
