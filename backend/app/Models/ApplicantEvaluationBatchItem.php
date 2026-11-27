<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicantEvaluationBatchItem extends Model
{
    protected $fillable = [
        'batch_id',
        'application_id',
        'analysis_id',
        'status',
        'error_message',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Parent evaluation batch.
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(
            ApplicantEvaluationBatch::class,
            'batch_id'
        );
    }

    /**
     * Applicant application.
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    /**
     * AI evaluation result.
     */
    public function analysis(): BelongsTo
    {
        return $this->belongsTo(AiAnalysis::class, 'analysis_id');
    }
}
