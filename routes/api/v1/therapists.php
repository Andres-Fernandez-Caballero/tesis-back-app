<?php

use App\Http\Controllers\Api\AvailabilityController;
use App\Http\Controllers\Api\TherapistController;
use Illuminate\Support\Facades\Route;

Route::prefix('therapists')
    ->group(function(){
        // Rutas publicas
        Route::get('/', [TherapistController::class, 'all'])->name('therapists.all');
        Route::get('/type/{type}', [TherapistController::class, 'getAllTherapistsByType'])->name('therapists.type');
        Route::post('/', [TherapistController::class, 'store'])->name('therapists.store');
        Route::get('/{id}', [TherapistController::class, 'details'])->name('therapists.details');
        Route::get('/{announcement}/availability', [AvailabilityController::class, 'list'])->name('therapists.announcements.list');

        Route::middleware('auth:sanctum')->group(function () {
        });
    });