<?php

use App\Http\Controllers\Api\PaymentsController;
use Illuminate\Support\Facades\Route;

Route::prefix('payments')
    ->group(function () {
        // Rutas publicas
        
        Route::post('/webhook/mercado-pago', function () {
            // Lógica para manejar el webhook de Mercado Pago
        });

        Route::get('/test', function () {
            // Lógica para obtener métodos de pago disponibles
            return "Métodos de pago disponibles: Tarjeta de crédito, Tarjeta de débito, Transferencia bancaria, Pago en efectivo";
        });
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/create-payment', [PaymentsController::class, 'createPaymentIntent']);
            Route::get('/transactions', function () {
                // Lógica;
            });
            Route::get('/{transaction_id}', function ($transaction_id) {
                // Lógica para obtener detalles de una transacción específica
            });
        });
    });
