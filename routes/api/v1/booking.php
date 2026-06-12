<?php

use App\Http\Controllers\Api\AnnoucementController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\ReviewController;
use Illuminate\Support\Facades\Route;

Route::prefix('bookings')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [BookingController::class, 'store'])->name('bookings.store');
        Route::get('/client', [BookingController::class, 'showClientBookings'])->name('bookings.client');
        Route::post('/{booking}/review', [ReviewController::class, 'store'])->name('bookings.review.store');
        // Polling: el cliente consulta el estado de pago de una reserva
        Route::get('/{booking}/payment-status', [BookingController::class, 'paymentStatus'])->name('bookings.payment-status');
        // Abandono: el cliente cerró el checkout sin pagar → cancelar la reserva
        Route::post('/{booking}/cancel-pending', [BookingController::class, 'cancelPending'])->name('bookings.cancel-pending');
    });

    Route::get('/', [BookingController::class, 'index'])->name('bookings.index');
});