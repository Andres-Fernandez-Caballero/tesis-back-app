<?php

use App\Http\Controllers\Api\AuthenticationController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')
        //->middleware('')
        ->group(function () {
    Route::post('register/client', [AuthenticationController::class, 'registerClient'])->name('auth.register.client');
    Route::post('register/therapist', [AuthenticationController::class, 'registerTherapist'])->name('auth.register.client');
    Route::post('/login', [AuthenticationController::class, 'login'])->name('auth.login');
    Route::post('forgot-password', [AuthenticationController::class, 'forgotPassword'])->name('auth.forgot-password');
    Route::post('/logout', [AuthenticationController::class, 'logout'])
        ->middleware('auth:sanctum')
        ->name('auth.logout');
    });
    