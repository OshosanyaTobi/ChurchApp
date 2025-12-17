<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;


class AuthController extends Controller
{
    /**
     * Register a new user
     *
     * @group Authentication
     * 
     * @bodyParam name string required The name of the user. Example: John Doe
     * @bodyParam email string required The email of the user. Example: johndoe@example.com
     * @bodyParam password string required The password of the user. Example: secret123
     * 
     * @response 201 {
     *   "user": {
     *     "id": 1,
     *     "name": "John Doe",
     *     "email": "johndoe@example.com",
     *     "updated_at": "2024-06-01T00:00:00.000000Z",
     *     "created_at": "2024-06-01T00:00:00.000000Z"
     *   },
     *   "token": "1|eyJ0eXAiOiJKV1QiLCJhbGciOi..."
     * }
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(['token' => $token, 'user' => $user], 201);
    }

    /**
     * Log in a user and return a token
     *
     * @group Authentication
     * 
     * @bodyParam email string required The user's email address. Example: johndoe@example.com
     * @bodyParam password string required The user's password. Example: secret123
     * 
     * @response 200 {
     *   "message": "Login successful",
     *   "token": "1|eyJ0eXAiOiJKV1QiLCJhbGciOi..."
     * }
     * 
     * @response 401 {
     *   "message": "Invalid credentials"
     * }
     */
    public function login(Request $request)
    {
        
        $credentials = $request->only('email','password');
        
        if (! $token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
        
        $user = Auth()->user();

        return response()->json([
            'message' =>'Logged in successfully',
            'token' => $token,
            'user'  => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                ]
            ]);
    }    

    public function me()
    {
        return response()->json(auth()->user());
    }


        /**$request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'message' => ['Invalid credentials'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
        ]);*/

    /**
     * Log out the currently authenticated user
     *
     * @group Authentication
     * 
     * @authenticated
     * 
     * @response 200 {
     *   "message": "Logged out"
     * }
     */
    public function logout(Request $request)
    {
        Auth::user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }
}
