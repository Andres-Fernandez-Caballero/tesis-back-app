<?php

use App\Http\Controllers\Api\AnnoucementController;
use Illuminate\Support\Facades\Route;

Route::prefix('announcements')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        //
    });
    Route::get('/', [AnnoucementController::class, 'index'])->name('announcements.index');
    Route::get('/destacates', [AnnoucementController::class, 'destacates'])->name('announcements.destacates');
    Route::get('/{id}', [AnnoucementController::class, 'show'])->name('announcements.show');
    // Route::post('/', [AnnoucementController::class, 'store']);
});