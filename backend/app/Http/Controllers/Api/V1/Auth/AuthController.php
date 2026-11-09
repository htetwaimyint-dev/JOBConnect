<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\HttpResopnseTrait;
use Illuminate\Http\Request;
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
            'password'    => 'required|string|min:6',
        ]);

        // 2. Check if validation failed
        if ($validator->fails()) {
            return $this->failResponse('Validation Fail', $validator->messages(), 422);
            
        }

        $validatedData = $validator->validated();
        // make new register
        $user = User::create($validatedData);

        // Generate token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Your logic to save to database goes here...
        return $this->successResponse('Register Successfully', $user, $token, 201);   
    }

    // LOGIN
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email:rfc',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return $this->failResponse(
                'Validation Failed',
                $validator->errors(),
                422
            );
        }

        // Get validated email and password
        $credentials = $validator->validated();

        // Check login credentials
        if (!Auth::attempt($credentials)) {
            return $this->failResponse(
                'Invalid email or password',
                null,
                401
            );
        }

        /** @var User $user */
        $user = Auth::user();

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse(
            'Login Successfully',
            $user,
            $token
        );
    }

    // GET LOGGED-IN USER
    public function user(Request $request)
    {
        return response()->json([
            'success' => true,
            'user' => $request->user(),
        ]);
    }
    // LOGOUT
    public function logout(Request $request)
    {
        // Delete only the current access token
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout Successfully',
        ]);
    }
}
