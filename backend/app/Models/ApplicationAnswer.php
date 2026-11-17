<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'job_question_id',
        'answer',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function jobQuestion(): BelongsTo
    {
        return $this->belongsTo(JobQuestion::class);
    }
}
