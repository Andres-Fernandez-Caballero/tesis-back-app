<?php

use App\Http\Controllers\Api\AvailabilityController;
use App\Http\Controllers\Api\TherapistController;
use Illuminate\Support\Facades\Route;

Route::prefix('notifications')
    ->group(function(){
        // Rutas publicas
        Route::post('/send-notification', [\App\Http\Controllers\Api\NotificationController::class, 'sendNotification']);
        
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\NotificationController::class, 'getNotifications']);
            Route::get('/unread',[\App\Http\Controllers\Api\NotificationController::class, 'getUnreadNotifications']);
            Route::patch("/read-all",[\App\Http\Controllers\Api\NotificationController::class, 'markAllAsRead']);
            Route::patch('/{id}/read', [\App\Http\Controllers\Api\NotificationController::class, 'markAsRead']);
            Route::post('/register-token', [\App\Http\Controllers\Api\NotificationController::class, 'registerToken']);
            Route::get("/unread/count", [\App\Http\Controllers\Api\NotificationController::class, 'countNotRead']);
        });
    });