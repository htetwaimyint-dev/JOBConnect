<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsJobSeeker
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()->role !== UserRole::JOB_SEEKER) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Job Seeker account required.',
            ], 403);
        }

        return $next($request);
    }
}
