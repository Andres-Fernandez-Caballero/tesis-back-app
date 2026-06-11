<?php

use App\Models\Therapists\Booking;
use App\Services\MercadoPagoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::prefix('payments')
    ->group(function () {

        // ─────────────────────────────────────────────────────────────────────
        // Webhook de Mercado Pago
        // POST /api/v1/payments/webhook/mercado-pago
        //
        // MP envía notificaciones con dos formatos:
        //   • IPN clásico: { topic: "payment", resource: "/v1/payments/123" }
        //   • Webhook moderno: { type: "payment", data: { id: "123" } }
        // Respondemos 200 inmediatamente y procesamos en background (sync por ahora).
        // ─────────────────────────────────────────────────────────────────────

        Route::post('/webhook/mercado-pago', function (Request $request) {
            // Responder 200 rápido para que MP no reintente
            // (el procesamiento es síncrono pero liviano)
            $payload = $request->all();
            Log::info('MercadoPago webhook recibido', $payload);

            $mpPaymentId = null;

            // Formato moderno: { type: "payment", data: { id: "123456" } }
            if (($payload['type'] ?? null) === 'payment') {
                $mpPaymentId = $payload['data']['id'] ?? null;
            }

            // Formato IPN clásico: { topic: "payment", resource: "/v1/payments/123456" }
            if (! $mpPaymentId && ($payload['topic'] ?? null) === 'payment') {
                $resource = $payload['resource'] ?? '';
                if (preg_match('/(\d+)$/', $resource, $m)) {
                    $mpPaymentId = $m[1];
                }
            }

            if (! $mpPaymentId) {
                // No es un evento de pago — ignorar silenciosamente
                return response()->json(['status' => 'ignored'], 200);
            }

            try {
                $mpService = app(MercadoPagoService::class);
                $mpPayment = $mpService->getPaymentById((int) $mpPaymentId);

                if (! $mpPayment) {
                    Log::warning("MercadoPago webhook: pago #{$mpPaymentId} no encontrado en MP.");
                    return response()->json(['status' => 'payment_not_found'], 200);
                }

                // external_reference = booking_id (seteado al crear la preference)
                $bookingId = $mpPayment->external_reference ?? null;

                if (! $bookingId) {
                    Log::warning("MercadoPago webhook: pago #{$mpPaymentId} sin external_reference.");
                    return response()->json(['status' => 'no_external_reference'], 200);
                }

                $booking = Booking::find((int) $bookingId);

                if (! $booking) {
                    Log::warning("MercadoPago webhook: booking #{$bookingId} no encontrado.");
                    return response()->json(['status' => 'booking_not_found'], 200);
                }

                DB::transaction(function () use ($mpService, $booking, $mpPayment) {
                    $booking->refresh();
                    $mpService->processPayment($booking, $mpPayment);
                });

                return response()->json(['status' => 'processed'], 200);

            } catch (\Throwable $e) {
                Log::error('MercadoPago webhook: error procesando notificación', [
                    'mp_payment_id' => $mpPaymentId,
                    'error'         => $e->getMessage(),
                    'trace'         => $e->getTraceAsString(),
                ]);

                // Devolver 200 de todos modos para que MP no reintente indefinidamente
                return response()->json(['status' => 'error'], 200);
            }
        })->name('payments.webhook.mercado-pago');

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
            Route::get('/transactions', function () {
                return response()->json(['message' => 'Próximamente.']);
            });
            Route::get('/{transaction_id}', function ($transaction_id) {
                return response()->json(['message' => 'Próximamente.', 'id' => $transaction_id]);
            });
        });
    });
