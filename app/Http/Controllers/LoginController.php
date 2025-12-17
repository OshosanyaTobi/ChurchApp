<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Check credentials against DB
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Login success
            return response()->json([
                'message' => 'Login successful',
                'user' => Auth::user()
            ], 200);
        }

        // Login failed
        return response()->json([
            'message' => 'Invalid credentials'
        ], 401);
    }
}
