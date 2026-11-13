<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        if ($request->user()->role !== UserRole::ADMIN) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Admin account required.',
            ], 403);
        }

        return $next($request);
    }
}
