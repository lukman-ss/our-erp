<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\AuthenticationController;

Route::post('/auth/register', [AuthenticationController::class, 'register']);
Route::post('/auth/login', [AuthenticationController::class, 'do_login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/auth/me', [AuthenticationController::class, 'me']);
    Route::post('/auth/refresh', [AuthenticationController::class, 'refresh']);
    Route::post('/auth/logout', [AuthenticationController::class, 'logout']);
});
