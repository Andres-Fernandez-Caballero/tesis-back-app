<?php

namespace App\Http\Controllers\Api;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Subscriptions\Plan;
use App\Models\Subscriptions\SubscriptionPayment;
use App\Services\MercadoPagoService;
use App\Services\SubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SubscriptionsController extends Controller
{
    public function __construct(
        private readonly SubscriptionService $subscriptionService,
    ) {}

    // ─────────────────────────────────────────────────────────────────────────
    // GET /api/v1/subscriptions/plans
    // ─────────────────────────────────────────────────────────────────────────
    public function plans(): JsonResponse
    {
        return response()->json(['data' => Plan::active()->get()]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /api/v1/subscriptions/me
    // ─────────────────────────────────────────────────────────────────────────
    public function me(Request $request): JsonResponse
    {
        $local = $request->user()->local;

        if (! $local) {
            return response()->json(['message' => 'El usuario no tiene un local asociado.'], 404);
        }

        return response()->json(['data' => $local->subscription?->load('plan')]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // POST /api/v1/subscriptions/subscribe  { plan_id }
    // Da de alta (o reutiliza) la suscripción del local y devuelve la URL de pago.
    // ─────────────────────────────────────────────────────────────────────────
    public function subscribe(Request $request): JsonResponse
    {
        $data = $request->validate([
            'plan_id' => ['required', 'integer', 'exists:plans,id'],
        ]);

        $local = $request->user()->local;

        if (! $local) {
            return response()->json(['message' => 'El usuario no tiene un local asociado.'], 404);
        }

        $plan = Plan::findOrFail($data['plan_id']);

        $subscription = $this->subscriptionService->subscribe($local, $plan);

        if ($subscription->isActive()) {
            return response()->json(['message' => 'La suscripción ya está activa.'], 400);
        }

        $result = $this->subscriptionService->createPaymentIntent($subscription);

        return response()->json([
            'message'     => 'Payment initiated',
            'payment_url' => $result['init_point'],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // POST /api/v1/subscriptions/webhook/mercado-pago
    // Mismo formato que el webhook de pagos de reservas (merchant_order IPN).
    // Siempre responde 200 para evitar reintentos de MP.
    // ─────────────────────────────────────────────────────────────────────────
    public function mercadoPagoWebhook(Request $request, MercadoPagoService $mpService): JsonResponse
    {
        $payload = $request->all();
        $type    = $payload['type'] ?? $payload['topic'] ?? null;
        Log::info('MercadoPago webhook de suscripción recibido', ['type' => $type, 'payload' => $payload]);

        try {
            return match (true) {
                $type === 'merchant_order'
                => $this->handleMerchantOrder((int) ($payload['id'] ?? 0), $mpService),

                $type === 'payment'
                => $this->handlePayment(
                    (int) ($payload['data']['id'] ?? $payload['data.id'] ?? 0),
                    $mpService
                ),

                default => response()->json(['status' => 'ignored'], 200),
            };
        } catch (\Throwable $e) {
            Log::error('MercadoPago webhook de suscripción: error procesando notificación', [
                'type'  => $type,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['status' => 'error'], 200);
        }
    }

    private function handleMerchantOrder(int $orderId, MercadoPagoService $mpService): JsonResponse
    {
        $order = $mpService->getMerchantOrderById($orderId);

        if (! $order) {
            Log::warning("MercadoPago webhook suscripción: merchant order #{$orderId} no encontrada.");
            return response()->json(['status' => 'order_not_found'], 200);
        }

        if (($order->status ?? null) !== 'closed') {
            return response()->json(['status' => 'order_not_closed'], 200);
        }

        $preferenceId = $order->preference_id ?? null;

        if (! $preferenceId) {
            Log::warning("MercadoPago webhook suscripción: orden #{$orderId} sin preference_id.");
            return response()->json(['status' => 'no_preference_id'], 200);
        }

        $payment = SubscriptionPayment::where('preference_id', $preferenceId)->first();

        if (! $payment) {
            Log::warning("MercadoPago webhook suscripción: no se encontró SubscriptionPayment para preference #{$preferenceId}.");
            return response()->json(['status' => 'local_payment_not_found'], 200);
        }

        $approvedPayments = collect($order->payments ?? [])
            ->filter(fn ($p) => ($p->status ?? null) === 'approved');

        if ($approvedPayments->isEmpty()) {
            Log::info("MercadoPago webhook suscripción: orden #{$orderId} sin pagos aprobados aún.");
            return response()->json(['status' => 'no_approved_payments'], 200);
        }

        $mpService->processSubscriptionPayment($payment->subscription, $payment);

        Log::info("MercadoPago webhook suscripción: orden #{$orderId} — pago aprobado para suscripción #{$payment->subscription_id}.");

        return response()->json(['status' => 'processed'], 200);
    }

    private function handlePayment(int $paymentId, MercadoPagoService $mpService): JsonResponse
    {
        if (! $paymentId) {
            Log::warning('MercadoPago webhook suscripción: notificación de payment sin id.');
            return response()->json(['status' => 'no_payment_id'], 200);
        }

        $mpPayment = $mpService->getPaymentById($paymentId);

        if (! $mpPayment) {
            Log::warning("MercadoPago webhook suscripción: payment #{$paymentId} no encontrado.");
            return response()->json(['status' => 'payment_not_found'], 200);
        }

        $subscriptionId = (int) ($mpPayment->external_reference ?? 0);

        $payment = SubscriptionPayment::where('subscription_id', $subscriptionId)
            ->where('status', PaymentStatus::PENDING)
            ->latest()
            ->first();

        if (! $payment) {
            Log::warning("MercadoPago webhook suscripción: no se encontró SubscriptionPayment pendiente para suscripción #{$subscriptionId} (payment #{$paymentId}).");
            return response()->json(['status' => 'local_payment_not_found'], 200);
        }

        $status = $mpPayment->status ?? null;

        if ($status === 'approved') {
            $mpService->processSubscriptionPayment($payment->subscription, $payment);

            Log::info("MercadoPago webhook suscripción: payment #{$paymentId} — pago aprobado para suscripción #{$payment->subscription_id}.");

            return response()->json(['status' => 'processed'], 200);
        }

        if (in_array($status, ['rejected', 'cancelled'], true)) {
            $payment->update([
                'status'      => PaymentStatus::FAILED,
                'external_id' => (string) $paymentId,
            ]);

            Log::info("MercadoPago webhook suscripción: payment #{$paymentId} con status '{$status}' — marcado como fallido para suscripción #{$subscriptionId}.");

            return response()->json(['status' => 'failed'], 200);
        }

        Log::info("MercadoPago webhook suscripción: payment #{$paymentId} con status '{$status}', aún no aprobado.");

        return response()->json(['status' => 'not_approved'], 200);
    }
}
