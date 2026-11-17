<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;   
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Services\Auth\AuthService;
use App\Traits\HttpResopnseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class AuthController extends Controller
{
    use HttpResopnseTrait;

    public function __construct(
        protected AuthService $authService
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $user = $this->authService->register($request->validated());
            return $this->successResponse('Register Successfully', $user, null, 201);
        } catch (Exception $e) {
            return $this->failResponse($e->getMessage(), null, $e->getCode() ?: 500);
        }
    }
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->login(
                $request->validated('email'),
                $request->validated('password'),
                $request->boolean('remember')
            );

            return $this->successResponse('Login Successfully', $result['user'], $result['token'], 200);
        } catch (Exception $e) {
            return $this->failResponse($e->getMessage(), null, $e->getCode() ?: 500);
        }
    }

    public function user(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'user'    => $request->user(),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $this->authService->logout($request->user());

            return response()->json([
                'message' => 'Logout Successfully',
                'status'  => 'success',
            ], 200);
        } catch (Exception $e) {
            return $this->failResponse('Logout failed', null, 500);
        }
    }
}