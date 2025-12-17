<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\WatchSectionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AudioController;

// Route::get('/api', function () {
//     return response()->json([
//         'status' => false,
//         'message' => "User is unauthenticated, kindly login"
//     ]);
// })->name('unauthenticated');

// $files = glob(__DIR__ . "/api/*.php");
// foreach ($files as $file) {
//     require($file);
// }

// Route::post('/register', [AuthController::class, 'register']);

// Route::post('/login', [AuthController::class, 'login']);


// Route::post('/password/forgot', [PasswordResetLinkController::class, 'sendOtp']);

// Route::post('/password/reset',  [PasswordResetLinkController::class, 'resetPassword']);

// Route::post('/paystack/initialize', [PaymentController::class, 'initialize']);

// Route::middleware(['auth:api', 'isAdmin'])->group(function () {
//     Route::post('/users', [UserController::class, 'user']);
//     Route::post('/blogs', [BlogController::class, 'store']);
//     Route::post('/watch-sections', [WatchSectionController::class, 'store']);
//     Route::post('/admins', [AdminController::class, 'store']); 
//     Route::post('/events', [EventController::class, 'store']); 
//     Route::post('/audio/upload', [AudioController::class, 'store']);
//     Route::put('/blogs/{id}', [BlogController::class, 'update']);
//     Route::put('/watch-sections/{id}', [WatchSectionController::class, 'update']);
//     Route::put('/events/{id}', [EventController::class, 'update']);
//     Route::get('/me', [AuthController::class, 'me']);
//     Route::get('/admins/{id}', [AdminController::class, 'show']); // view admin details
//     Route::delete('/blogs/{id}', [BlogController::class, 'destroy']);
//     Route::delete('/watch-sections/{id}', [WatchSectionController::class, 'destroy']);
//     Route::delete('/events/{id}', [EventController::class, 'destroy']);
//     Route::delete('audio/{id}', [AudioController::class, 'destroy']);
// });




// // Route::get('/test-auth', function () {
// //     return response()->json(['user' => auth()->id()]);
// // })->middleware('auth:api');


// // Route::middleware('auth:sanctum')->group(function () {
//     // Route::get('/user', [AuthController::class, 'user']);
//     // Route::post('/logout', [AuthController::class, 'logout']);
// // });

// Route::middleware(['auth:api', 'isUser'])->group(function () {

// Route::get('/audio', [AudioController::class, 'index']);

// Route::get('/audio/remote', [AudioController::class, 'listFromBytescale']);

// Route::get('/paystack/callback', [PaymentController::class, 'callback'])->name('paystack.callback');

// Route::get('/events', [EventController::class, 'index']);

// Route::get('/watch-sections', [WatchSectionController::class, 'index']);

// Route::get('/blogs', [BlogController::class, 'index']);

// Route::get('/users', [UserController::class, 'user']);

// // Route::get('/login', [LoginController::class, 'login']);
// });

// Route::get('/rest', function () {
//     return response()->json([
//         'status' => false,
//         'message' => "Welcome",
//     ]);
// });