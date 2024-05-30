<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeEmail; // Import the welcome email Mailable class

class ApiController extends Controller
{
    /**
     * Register a new user.
     *
     * @group Authentication
     * 
     * @bodyParam name string required The name of the user. Example: Adetunji Phillip
     * @bodyParam email string required The email of the user. Example: phillip@email.com
     * @bodyParam password string required The password of the user. Example: 12345678
     * 
     * @response 201 {
     *  "message": "User registered successfully",
     *  "status": true,
     *  "user": {
     *    "id": 1,
     *    "name": "Adetunji Phillip",
     *    "email": "phillip@mail.com",
     *    "created_at": "2024-05-30T00:00:00.000000Z",
     *    "updated_at": "2024-05-30T00:00:00.000000Z"
     *  }
     * }
     * 
     * @response 400 {
     *   "name": ["The name field is required."],
     *   "email": ["The email field is required."],
     *   "password": ["The password field is required."]
     * }
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Create User
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        // Send Welcome Email
        Mail::to($user->email)->send(new WelcomeEmail($user));

        return response()->json([
            "message" => "User registered successfully",
            "status" => true,
            'user' => $user
        ], 201);
    }

    /**
     * Login
     *
     * @group Authentication
     * 
     * @bodyParam email string required The email of the user. Example: sakinropo@gmail.com
     * @bodyParam password string required The password of the user. Example: 12345678
     * 
     * @response {
     *  "message": "Login successful",
     *  "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
     *  "token_type": "Bearer"
     * }
     * 
     * @response 400 {
     *   "email": ["The email field is required."],
     *   "password": ["The password field is required."]
     * }
     * 
     * @response 401 {
     *   "message": "Invalid login credentials"
     * }
     */
    public function login(Request $request)
    {
        // Data validation
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Checking User Login
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid login credentials'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->accessToken;

        return response()->json([
            'message' => 'Login successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Get the authenticated user's profile.
     *
     * @group Authentication
     * 
     * @response {
     *  "status": true,
     *  "message": "User profile information",
     *  "data": {
     *    "id": 1,
     *    "name": "Adetunji Phillip",
     *    "email": "phillip@mail.com",
     *    "created_at": "2024-05-30T00:00:00.000000Z",
     *    "updated_at": "2024-05-30T00:00:00.000000Z"
     *  }
     * }
     */
    public function profile()
    {
        $user = Auth::user();

        return response()->json([
            "status" => true,
            "message" => 'User profile information',
            "data" => $user,
        ]);
    }

    /**
     * Logout the authenticated user.
     *
     * @group Authentication
     * 
     * @response {
     *  "status": true,
     *  "message": "Logout successful"
     * }
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        $user->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logout successful',
        ]);
    }
}

