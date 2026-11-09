<?php

namespace App\Traits;

trait HttpResopnseTrait
{
     public function successResponse($message, $user, $token, $code = 200){
        return response()->json([
                'message' => $message,
                'success' => true,
                'user'    => $user,
                'token'   => $token,
            ], $code);     
    }

    public function failResponse($message, $error, $code = 500){
        return response()->json([
            'message' => $message,
            'success' => false,
            'data'    => $error,
        ],$code);
    }
}
