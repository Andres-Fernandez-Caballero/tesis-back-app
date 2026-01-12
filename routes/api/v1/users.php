<?php

use App\Http\Controllers\Api\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::prefix('users')->group(function () {

    // Públicos
    Route::get('/', [UserManagementController::class, 'index'])
        ->name('users.index');

    Route::get('/therapists', [UserManagementController::class, 'therapists'])
        ->name('users.therapists');

    Route::get('/clients', [UserManagementController::class, 'clients'])
        ->name('users.clients');

    // Protegidos
    Route::middleware('auth:sanctum')->group(function () {

        Route::get('/me', [UserManagementController::class, 'show'])
            ->name('users.me');

        Route::put('/me', [UserManagementController::class, 'update'])
            ->name('users.update');
    });
});
