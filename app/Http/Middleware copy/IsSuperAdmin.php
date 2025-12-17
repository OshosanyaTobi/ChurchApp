<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (
            auth()->check() &&
            (auth()->user()->role = 'super_admin')
            // &&
            // !auth()->user()->is_first_login
        ) {
            return $next($request);
        }

        return response()->json([
            'status' => false,
            'message' => 'Unauthorized.'
        ], 403);
    }
}
