<?php

namespace App\Traits;

trait HttpResopnseTrait
{
    public function successResponse(string $message, mixed $user, mixed $token, int $code = 200)
    {
        return response()->json([
            'message' => $message,
            'success' => true,
            'user'    => $user,
            'token'   => $token,
        ], $code);
    }

    public function failResponse(string $message, mixed $error, int $code = 500)
    {
        return response()->json([
            'message' => $message,
            'success' => false,
            'data'    => $error,
        ], $code);
    }
}
