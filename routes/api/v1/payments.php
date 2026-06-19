<?php

use App\Http\Controllers\Api\PaymentsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::prefix('payments')
    ->group(function () {

        // POST /api/v1/payments/webhook/mercado-pago
        Route::post('/webhook/mercado-pago', [PaymentsController::class, 'mercadoPagoWebhook'])
            ->name('payments.webhook.mercado-pago');

        // ─────────────────────────────────────────────────────────────────────
        // Retorno de Mercado Pago (back_urls)
        // GET /api/v1/payments/mp-return?status=...&booking_id=...
        //
        // MP redirige aquí después del checkout. Como la app usa el external
        // browser de Expo, esta URL es sólo un puente — simplemente devuelve
        // un JSON que el AppState listener del frontend puede ignorar.
        // El estado real se consulta con el endpoint de polling.
        // ─────────────────────────────────────────────────────────────────────

        Route::get('/mp-return', function (Request $request) {
            $status    = $request->query('status', 'pending');
            $bookingId = $request->query('booking_id');

            Log::info('MercadoPago retorno desde checkout', [
                'status'     => $status,
                'booking_id' => $bookingId,
            ]);

            return response()->json([
                'message'    => 'Pago recibido. Verificando con el servidor...',
                'status'     => $status,
                'booking_id' => $bookingId,
            ]);
        })->name('payments.mp-return');

        // ─────────────────────────────────────────────────────────────────────
        // Rutas autenticadas heredadas
        // ─────────────────────────────────────────────────────────────────────

        Route::middleware('auth:sanctum')->group(function () {
            /* create payment intent genera un payment abstracto en el sistema 
            y devuelve un PaymentResult con init_point para redirigir al checkout. 
            */
            Route::post('/create-payment-intent',  [PaymentsController::class, 'createPaymentIntent'])
                ->name('payments.create_payment_intent');

            Route::get('/transactions', function () {
                return response()->json(['message' => 'Próximamente.']);
            });
            Route::get('/{transaction_id}', function ($transaction_id) {
                return response()->json(['message' => 'Próximamente.', 'id' => $transaction_id]);
            });
        });
    });
