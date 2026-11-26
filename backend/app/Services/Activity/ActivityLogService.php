<?php

namespace App\Services\Activity;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLogService
{
    public function log(
        string $action,
        ?string $description = null,
        ?Model $subject = null
    ): ActivityLog {
        return ActivityLog::create([
            'actor_id' => Auth::id(),
            'action' => $action,
            'description' => $description,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->getKey(),
            'ip_address' => request()->ip(),
            'created_at' => now(),
        ]);
    }
}
