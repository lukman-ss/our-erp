<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\Frontend\AuthenticationController;

Route::get('/login',  [AuthenticationController::class, 'login'])->name('login');

use App\Http\Controllers\Frontend\DashboardController;

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});


