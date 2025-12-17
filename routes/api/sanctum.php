<?php

use App\Http\Controllers\SanctumController;
use Illuminate\Support\Facades\Route;


Route::group(
    ["middleware" => []],
    function () {
        Route::post('/sanctum/token', [SanctumController::class, 'issueToken']);
    }
);