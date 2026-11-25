<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Interviewer extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'position',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function interviews(): BelongsToMany
    {
        return $this->belongsToMany(Interview::class, 'interview_interviewer')
            ->withTimestamps();
    }
}
