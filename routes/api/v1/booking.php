<?php

use App\Http\Controllers\Api\AnnoucementController;
use App\Http\Controllers\Api\BookingController;
use Illuminate\Support\Facades\Route;

Route::prefix('bookings')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [BookingController::class, 'store'])->name('bookings.store');
        Route::get('/client', [BookingController::class, 'showClientBookings'])->name('bookings.client');
    });
        
    Route::get('/', [BookingController::class, 'index'])->name('bookings.index');
});