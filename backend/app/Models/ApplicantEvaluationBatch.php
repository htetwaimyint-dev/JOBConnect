<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApplicantEvaluationBatch extends Model
{
    protected $fillable = [
        'job_id',
        'requested_by',
        'total_applicants',
        'status',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'total_applicants' => 'integer',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Job being evaluated.
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    /**
     * Employer who requested the evaluation.
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * Applicants included in this batch.
     */
    public function items(): HasMany
    {
        return $this->hasMany(ApplicantEvaluationBatchItem::class, 'batch_id');
    }
}
