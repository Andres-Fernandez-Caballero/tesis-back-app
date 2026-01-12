<?php

use App\Http\Controllers\Api\AuthenticationController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')
        //->middleware('')
        ->group(function () {
    Route::post('register', [AuthenticationController::class, 'register'])->name('auth.register');
    Route::post('/login', [AuthenticationController::class, 'login'])->name('auth.login');
    Route::post('forgot-password', [AuthenticationController::class, 'forgotPassword'])->name('auth.forgot-password');
    Route::post('/logout', [AuthenticationController::class, 'logout'])
        ->middleware('auth:sanctum')
        ->name('auth.logout');
    });
    