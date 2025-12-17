<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function user()
    {
        // Get all users
        $users = User::all();

        // Return as JSON
        return response()->json([
            'users' => $users
        ], 200);
    }
}
