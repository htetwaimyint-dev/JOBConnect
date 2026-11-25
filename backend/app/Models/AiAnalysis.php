<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'job_id',
        'application_id',
        'requested_by',
        'analysis_type',
        'input_data',
        'input_hash',
        'result',
        'provider',
        'model',
        'status',
    ];
    protected function casts(): array
    {
        return [
            'input_data' => 'array',
            'input_hash' => 'string',
            'result' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    // AI Analysis belongs to one Application
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_id');
    }

    // AI Analysis was requested by one Employer
    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function recommendations(): HasMany
    {
        return $this->hasMany(AiRecommendation::class, 'analysis_id');
    }
}
