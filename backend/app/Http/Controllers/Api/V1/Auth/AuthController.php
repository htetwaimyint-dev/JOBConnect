<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\HttpResopnseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use HttpResopnseTrait;

    // REGISTER
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'email'       => 'required|email:rfc,dns|unique:users',
            'password'    => 'required|string|min:6|confirmed',
            'role'     => 'required|in:job_seeker,employer',

        ]);
        // 2. Check if validation failed
        if ($validator->fails()) {
            return $this->failResponse('Validation Fail', $validator->messages(), 422);
        }

        $validatedData = $validator->validated();
        // 4. Create new user with default role
        $user = User::create([
            'name'     => $validatedData['name'],
            'email'    => $validatedData['email'],
            'password' => $validatedData['password'],
            'role'     => $validatedData['role'],
            'status'   => 'active',
        ]);

        // Generate token
        // $token = $user->createToken('auth_token')->plainTextToken;

        // Your logic to save to database goes here...
        return $this->successResponse('Register Successfully', $user, null, 201);
    }

    // LOGIN
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
            'remember' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return $this->failResponse(
                'Validation Fail',
                $validator->errors(),
                422
            );
        }

        // Get request data
        $email = $request->email;
        $password = $request->password;

        // Find user from database
        $user = User::where('email', $email)->first();

        if (!$user) {
            return $this->failResponse('User not found', null, 404);
        }

        // Check user status
        if ($user->status !== UserStatus::ACTIVE) {
            return $this->failResponse(
                'Your account is not active',
                null,
                403
            );
        }


        // Compare request password with database password
        if (!Hash::check($password, $user->password)) {
            return $this->failResponse('Invalid password', null, 401);
        }

        //  Remember Me
        $remember = $request->boolean('remember');

        $expiresAt = $remember
            ? now()->addDays(7)
            : now()->addDay();

        //  Generate token
        $token = $user->createToken(
            'auth_token',
            ['*'],
            $expiresAt
        )->plainTextToken;

        // Generate API Token
        //$token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse(
            'Login Successfully',
            $user,
            $token,
            200
        );
    }


    //GET LOGGED-IN USER
    public function user(Request $request)
    {
        return response()->json([
            'success' => true,
            'user' => $request->user(),
        ]);
    }

    //LOGOUT
    public function logout(Request $request)
    {
        //  $user = $request->user()->tokens->delete();
        $user = $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Logout Successfully',
            'status'  => 'successes',
            'user'    => $user,
        ], 200);
    }
}
