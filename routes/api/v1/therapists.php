<?php

use App\Http\Controllers\Api\TherapistController;
use Illuminate\Support\Facades\Route;

Route::prefix('therapists')
    ->group(function(){
        Route::get('/', [TherapistController::class, 'all'])->name('therapists.all');
        Route::get('/type/{type}', [TherapistController::class, 'getAllTherapistsByType'])->name('therapists.type');
        Route::post('/', [TherapistController::class, 'store'])->name('therapists.store');
        Route::get('/{id}', [TherapistController::class, 'details'])->name('therapists.details');
    });