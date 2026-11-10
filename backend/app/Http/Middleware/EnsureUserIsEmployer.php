<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsEmployer
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()->role !== UserRole::EMPLOYER) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Employer account required.',
            ], 403);
        }

        return $next($request);
    }
}
