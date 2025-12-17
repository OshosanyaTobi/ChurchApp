<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of exception types that are not reported.
     */
    protected $dontReport = [];

    /**
     * The list of inputs that are never flashed.
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Handle unauthenticated users (API-only).
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return response()->json([
            'status' => false,
            'message' => 'Unauthenticated'
        ], 401);
    }

    public function register(): void
    {
        //
    }

    public function render($request, Throwable $exception)
    {
        if ($request->is('api/*')) {
            return response()->json([
                'status' => false,
                'message' => $exception->getMessage()
            ], method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 500);
        }

        return parent::render($request, $exception);
    }
}
