<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // Create admin
    public function store(Request $request)
    {
        // Only an existing admin can create another admin
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $admin = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
        ]);

        return response()->json(['message' => 'Admin created', 'admin' => $admin], 201);
    }

    // View admin details
    public function show($id)
    {
        $admin = User::where('id', $id)->where('role', 'admin')->firstOrFail();
        return response()->json($admin);
    }
}