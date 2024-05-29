<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


/**
 * @group Authentication
 *
 * APIs for user authentication
 */

class ApiController extends Controller
{
    /**
     * Register
     *
     * @bodyParam name string required The name of the user. Example: Adetunji Phillip
     * @bodyParam email string required The email of the user. Example: phillip@email.com
     * @bodyParam password string required The password of the user. Example: 12345678
     * 
     * @response {
     *  "name": "Adetunji Phillip",
     *  "email": "phillip@mail.com",
     * }
     */
    
    // Register API (POST)
    public function register(Request $request){
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

        return response()->json([
            "message" => "User registered successfully",
            "status" => true,
            'user' => $user
        ], 201);

    }

    /**
     * Register
     *
     * @bodyParam email string required The email of the user. Example: sakinropo@gmail.com
     * @bodyParam password string required The password of the user. Example: 12345678
     * 
     * @response {
     *  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
     * }
     */

    // Profile API (POST)
    public function login(Request $request){
        
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
    // Profile API (GET)
    public function profile(){
        $user = Auth::user();

        return response()->json([
            "status" => true,
            "message" => 'User profile information',
            "data" => $user,
        ]);

    }
    // logout API (POST)
    public function logout(Request $request){
        $user = Auth::user();
        $user->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logout successful',
        ]);
    }
}
