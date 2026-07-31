<?php

use App\Http\Controllers\Api\SubscriptionsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::prefix('subscriptions')
    ->group(function () {

        // POST /api/v1/subscriptions/webhook/mercado-pago
        Route::post('/webhook/mercado-pago', [SubscriptionsController::class, 'mercadoPagoWebhook'])
            ->name('subscriptions.webhook.mercado-pago');

        // ─────────────────────────────────────────────────────────────────────
        // Retorno de Mercado Pago (back_urls). El dueño de local paga desde el
        // panel Filament ("app"), así que el puente redirige ahí directamente.
        // GET /api/v1/subscriptions/mp-return?status=...&subscription_id=...
        // ─────────────────────────────────────────────────────────────────────
        Route::get('/mp-return', function (Request $request) {
            $status         = $request->query('status', 'pending');
            $subscriptionId = $request->query('subscription_id');

            Log::info('MercadoPago retorno desde checkout de suscripción', [
                'status'          => $status,
                'subscription_id' => $subscriptionId,
            ]);

            return redirect()->route('filament.app.pages.suscripcion', ['status' => $status]);
        })->name('subscriptions.mp-return');

        Route::get('/plans', [SubscriptionsController::class, 'plans']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/me', [SubscriptionsController::class, 'me']);
            Route::post('/subscribe', [SubscriptionsController::class, 'subscribe']);
        });
    });
