<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobSeekerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_seeker_id',
        'profile_photo',
        'phone',
        'date_of_birth',
        'gender',
        'address',
        'city',
        'professional_title',
        'bio',
        'expected_salary',
        'experience_years',
        'linkedin_url',
        'portfolio_url',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'expected_salary' => 'decimal:2',
            'experience_years' => 'decimal:1',
        ];
    }

    // Profile belongs to Job Seeker (User)
    public function jobSeeker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'job_seeker_id');
    }
}
