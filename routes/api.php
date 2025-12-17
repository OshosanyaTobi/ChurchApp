<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\WatchSectionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AudioController;




/*
|--------------------------------------------------------------------------
| Public Routes (Unauthenticated)
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return response()->json([
        'status' => false,
        'message' => 'API is running. Please login.'
    ]);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/password/forgot', [PasswordResetLinkController::class, 'sendOtp']);
Route::post('/password/reset', [PasswordResetLinkController::class, 'resetPassword']);
Route::post('/paystack/initialize', [PaymentController::class, 'initialize']);
Route::get('/paystack/callback', [PaymentController::class, 'callback']);
Route::get('/rest', function () {
    return response()->json([
        'status' => false,
        'message' => 'Welcome'
    ]);
});

/*
|--------------------------------------------------------------------------
| Authenticated Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:api', 'isAdmin'])->group(function () {
    // Admin CRUD
    Route::post('/admins', [AdminController::class, 'store']);
    Route::get('/admins/{id}', [AdminController::class, 'show']);

    // Blog, Event, WatchSection management
    Route::post('/blogs', [BlogController::class, 'store']);
    Route::get('/get-blogs', [BlogController::class, 'index']);
    Route::put('/blogs/{id}', [BlogController::class, 'update']);
    Route::delete('/blogs/{id}', [BlogController::class, 'destroy']);

    Route::get('/get-events', [EventController::class, 'index']);
    Route::post('/events', [EventController::class, 'store']);
    Route::put('/events/{id}', [EventController::class, 'update']);
    Route::delete('/events/{id}', [EventController::class, 'destroy']);

    Route::get('/get-watch-sections', [WatchSectionController::class, 'index']);
    Route::post('/watch-sections', [WatchSectionController::class, 'store']);
    Route::put('/watch-sections/{id}', [WatchSectionController::class, 'update']);
    Route::delete('/watch-sections/{id}', [WatchSectionController::class, 'destroy']);

    // Audio management
    Route::post('/audio/upload', [AudioController::class, 'store']);
    Route::delete('/audio/{id}', [AudioController::class, 'destroy']);

    // Users (admin-only access)
    Route::post('/users', [UserController::class, 'user']);

    // Profile
    Route::get('/me', [AuthController::class, 'me']);
});

Route::get('/audio', [AudioController::class, 'index']);
/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:api', 'isUser'])->group(function () {
    Route::get('/audio/remote', [AudioController::class, 'listFromBytescale']);
    Route::get('/events', [EventController::class, 'index']);
    Route::get('/watch-sections', [WatchSectionController::class, 'index']);
    Route::get('/blogs', [BlogController::class, 'index']);
    Route::get('/users', [UserController::class, 'user']);
});

/*
|--------------------------------------------------------------------------
| Fallback Route (JSON Only)
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
    return response()->json([
        'status' => false,
        'message' => 'Endpoint not found'
    ], 404);
});
