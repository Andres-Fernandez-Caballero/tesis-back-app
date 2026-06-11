<?php

use App\Http\Controllers\Api\LocalController;
use Illuminate\Support\Facades\Route;

Route::prefix('locals')->group(function () {
    Route::get('/',                        [LocalController::class, 'index']);
    Route::get('/{local}/especialidades',  [LocalController::class, 'especialidades']);
    Route::get('/{local}/slots',           [LocalController::class, 'slots']);
    Route::get('/{local}/masajistas',      [LocalController::class, 'masajistas']);
    Route::post('/{local}/bookings',       [LocalController::class, 'createBooking'])
        ->middleware('auth:sanctum');
});
