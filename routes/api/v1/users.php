<?php

use App\Http\Controllers\Api\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::prefix('users')->group(function () {

    // Públicos
    Route::get('/', [UserManagementController::class, 'getAllUsers'])
        ->name('users.index');

    Route::get('/therapists', [UserManagementController::class, 'getAllTherapists'])
        ->name('users.therapists');

    Route::get('/clients', [UserManagementController::class, 'getAllClients'])
        ->name('users.clients');

    // Protegidos
    Route::middleware('auth:sanctum')->group(function () {

        Route::get('/me', [UserManagementController::class, 'show'])
            ->name('users.me');

        Route::put('/me', [UserManagementController::class, 'update'])
            ->name('users.update');

        Route::post('/me/photo', [UserManagementController::class, 'uploadPhoto'])
            ->name('users.photo');
    });
});
