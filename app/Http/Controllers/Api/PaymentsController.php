<?php

namespace App\Http\Controllers\Api;

use App\Core\UseCases\Payments\PaymentMethodFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Payments\CreatePaymentRequest as PaymentsCreatePaymentRequest;
use App\Models\Payments\Payment;
use App\Models\Therapists\Booking;
use App\Services\MercadoPagoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentsController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // POST /api/v1/payments/create-payment-intent
    // Crea un Payment local y una preferencia de MP, devuelve la URL de pago
    // ─────────────────────────────────────────────────────────────────────────
    public function createPaymentIntent(PaymentsCreatePaymentRequest $request): JsonResponse
    {

        DB::beginTransaction();
        try {
            $booking = Booking::findOrFail($request->booking_id);

            if ($booking->transaction->hasApprovedPayment()) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Ya existe un pago aprobado para esta reserva.',
                ], 400);
            }

            $paymentResult = PaymentMethodFactory::create($request->payment_method)
                ->processPayment(booking: $booking, platform: $request->input('platform', 'web'));

            DB::commit();

            return response()->json([
                'message'     => 'Payment initiated',
                'payment_url' => $paymentResult->payment_url,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment processing error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
            return response()->json([
                'message' => 'No se pudo procesar el pago. Por favor intentá de nuevo.',
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // POST /api/v1/payments/webhook/mercado-pago
    //
    // MP puede enviar dos formatos:
    //   • Moderno:  { type: "payment", data: { id: "123" } }
    // Siempre responde 200 para evitar reintentos de MP.
    // ─────────────────────────────────────────────────────────────────────────
    public function mercadoPagoWebhook(Request $request, MercadoPagoService $mpService): JsonResponse
    {
        $payload = $request->all();
        $type    = $payload['type'] ?? $payload['topic'] ?? null;
        Log::debug('MercadoPago webhook payload', ['payload' => $payload]);
        Log::info('MercadoPago webhook recibido', ['type' => $type, 'payload' => $payload]);

        try {
            return match (true) {
                // IPN: MP envía topic=merchant_order&id=<order_id> como query params
                $type === 'merchant_order'
                => $this->handleMerchantOrder((int) ($payload['id'] ?? 0), $mpService),

                default => response()->json(['status' => 'ignored'], 200),
            };
        } catch (\Throwable $e) {
            Log::error('MercadoPago webhook: error procesando notificación', [
                'type'  => $type,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['status' => 'error'], 200);
        }
    }

    // Procesa una merchant order cerrada — busca el booking por preference_id local.
    private function handleMerchantOrder(int $orderId, MercadoPagoService $mpService): JsonResponse
    {
        $order = $mpService->getMerchantOrderById($orderId);

        if (! $order) {
            Log::warning("MercadoPago webhook: merchant order #{$orderId} no encontrada.");
            return response()->json(['status' => 'order_not_found'], 200);
        }

        if (($order->status ?? null) !== 'closed') {
            return response()->json(['status' => 'order_not_closed'], 200);
        }

        // Buscar el Payment local por preference_id para obtener el booking
        $preferenceId = $order->preference_id ?? null;

        if (! $preferenceId) {
            Log::warning("MercadoPago webhook: orden #{$orderId} sin preference_id.");
            return response()->json(['status' => 'no_preference_id'], 200);
        }

        $localPayment = Payment::where('preference_id', $preferenceId)->first();

        if (! $localPayment) {
            Log::warning("MercadoPago webhook: no se encontró Payment local para preference #{$preferenceId}.");
            return response()->json(['status' => 'local_payment_not_found'], 200);
        }

        $booking = $localPayment->transaction->booking;

        if (! $booking) {
            Log::warning("MercadoPago webhook: no se encontró booking para preference #{$preferenceId}.");
            return response()->json(['status' => 'booking_not_found'], 200);
        }

        // Procesar cada pago aprobado de la orden (normalmente solo hay uno)
        $approvedPayments = collect($order->payments ?? [])
            ->filter(fn($p) => ($p->status ?? null) === 'approved');

        if ($approvedPayments->isEmpty()) {
            Log::info("MercadoPago webhook: orden #{$orderId} sin pagos aprobados aún.");
            return response()->json(['status' => 'no_approved_payments'], 200);
        }

        $mpService = app(MercadoPagoService::class);
        $mpService->processPayment($booking, $localPayment);

        Log::info("MercadoPago webhook: orden #{$orderId} — pago aprobado para booking #{$booking->id}.");

        return response()->json(['status' => 'processed'], 200);
    }
}
