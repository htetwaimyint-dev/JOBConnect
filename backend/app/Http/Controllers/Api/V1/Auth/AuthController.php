<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Traits\HttpResopnseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use HttpResopnseTrait;

    // REGISTER
    public function register(Request $request)
    {
        // 1. Define rules and incoming data
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
        // 4. Create new user with default role
       $user = User::create([
        'name'     => $validatedData['name'],
        'email'    => $validatedData['email'],
        'password' => $validatedData['password'],
        'role' => 'required|in:job_seeker,employer',
    ]);

        // Generate token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Your logic to save to database goes here...
        return $this->successResponse('Register Successfully', $user, $token, 201);   

    }

    // LOGIN
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => 'required|email:rfc,dns',
            'password' => 'required|string|min:6',
        ]);

        if($validator->fails()){
            return $this->failResponse('Validation Fail', $validator->messages(), 422);
        }

        $validatedData = $validator->validate();
        if(Auth::attempt($validatedData)){
            $user = Auth::user(); 
            // genetate tokens & store tokens to db [ $user = Auth::user() / $user = $request->user() ]
            $token = $request->user()->createToken('auth_token')->plainTextToken;
            // response to client
           return $this->successResponse('Login Successfully', $user, $token);
        }
        else{
           return $this->failResponse('Credential Fails', $validator->messages(),401);
        }
    }

    // GET LOGGED-IN USER
    public function user(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }

    // LOGOUT
    public function logout(Request $request)
    {
         $user = $request->user()->tokens->delete();
         return response()->json([
                'message' => 'Logout Successfully',
                'status'  => 'successes',
                'user'    => $user,
            ], 200);    
    }
}
