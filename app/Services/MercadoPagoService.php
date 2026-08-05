<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Enums\SubscriptionStatus;
use App\Models\Payments\Payment;
use App\Models\Subscriptions\Subscription;
use App\Models\Subscriptions\SubscriptionPayment;
use App\Models\Therapists\Booking;
use App\Models\Therapists\States\Booking\BookingCancelled;
use App\Models\Therapists\States\Booking\BookingCompleted;
use App\Models\Therapists\States\Booking\BookingConfirmed;
use App\Models\Therapists\States\Booking\BookingExpired;
use App\Notifications\UserNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use MercadoPago\Client\MerchantOrder\MerchantOrderClient;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Client\Payment\PaymentRefundClient;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\MercadoPagoConfig;

class MercadoPagoService
{
    public function __construct()
    {
        MercadoPagoConfig::setAccessToken(config('mercadopago.access_token'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Crea una preference de Checkout Pro y devuelve un array con la URL de pago.
    // return ['init_point', 'preference_id']
    // ─────────────────────────────────────────────────────────────────────────

    public function createPreference(Booking $booking, string $platform = 'web'): array
    {
        MercadoPagoConfig::setAccessToken(env('MP_ACCESS_TOKEN'));

        $mobileScheme = config('mercadopago.mobile_scheme', 'bodyfix');
        $bookingId    = $booking->id;

        // Native: openAuthSessionAsync detecta el redirect al scheme y cierra el browser.
        // Web: redirige a la URL del frontend configurada en FRONTEND_URL.
        if ($platform === 'native') {
            $backUrls = [
                'success' => "{$mobileScheme}://payment-callback?status=success&booking_id={$bookingId}",
                'failure' => "{$mobileScheme}://payment-callback?status=failure&booking_id={$bookingId}",
                'pending' => "{$mobileScheme}://payment-callback?status=pending&booking_id={$bookingId}",
            ];
        } else {
            // back_url apunta al backend (siempre público) que luego redirige al frontend.
            // Esto evita que MP rechace URLs de localhost en desarrollo.
            $backUrls = [
                'success' => route('payments.mp-return', ['status' => 'success', 'booking_id' => $bookingId]),
                'failure' => route('payments.mp-return', ['status' => 'failure', 'booking_id' => $bookingId]),
                'pending' => route('payments.mp-return', ['status' => 'pending', 'booking_id' => $bookingId]),
            ];
        }

        $payload = [
            'items' => [[
                'id'          => 'booking_' . $bookingId,
                'title'       => "Booking #{$bookingId}",
                'quantity'    => 1,
                'unit_price'  => (float) $booking->transaction->amount,
                'currency_id' => 'ARS',
            ]],
            'notification_url'   => route('payments.webhook.mercado-pago'),
            'external_reference' => (string) $bookingId,
            'back_urls'          => $backUrls,
            'auto_return'        => 'approved',
        ];

        Log::debug('MercadoPagoService: payload para crear preference', ['payload' => $payload]);

        try {
            $client     = new PreferenceClient();
            $preference = $client->create($payload);
        } catch (\MercadoPago\Exceptions\MPApiException $e) {
            // Loguear la respuesta completa de MP para diagnóstico
            $apiResponse = $e->getApiResponse();
            Log::error('MercadoPagoService: error creando preference', [
                'status_code'   => $apiResponse?->getStatusCode(),
                'response_body' => $apiResponse?->getContent(),
                'payload_sent'  => $payload,
            ]);
            throw $e;
        }
        Log::debug('MercadoPagoService: preference CREADA', [
            'preference_id' => $preference->id,
            'notification_url' => $preference->notification_url,
            'external_reference' => $preference->external_reference,
            'init_point'    => $preference->init_point,
            'sandbox_init_point' => $preference->sandbox_init_point,
        ]);
        // En sandbox usar sandbox_init_point; en producción usar init_point
        $isSandbox = config('mercadopago.sandbox');
        $url = $isSandbox ? $preference->sandbox_init_point : $preference->init_point;

        if (empty($url)) {
            Log::warning('MercadoPagoService: init_point vacío', [
                'sandbox'    => $isSandbox,
                'preference' => (array) $preference,
            ]);
            // Fallback: si sandbox_init_point está vacío, intentar con init_point
            $url = $preference->init_point ?? '';
        }

        return [
            'init_point' => $url,
            'preference_id' => $preference->id,
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Obtiene una merchant order de MP por su ID.
    // La order agrupa los pagos asociados a una preference.
    // ─────────────────────────────────────────────────────────────────────────

    public function getMerchantOrderById(int $orderId): ?object
    {
        try {
            $client = new MerchantOrderClient();
            return $client->get($orderId);
        } catch (\Throwable $e) {
            Log::warning("MercadoPagoService: error obteniendo merchant order #{$orderId}", [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Obtiene un pago de MP por su ID.
    // ─────────────────────────────────────────────────────────────────────────

    public function getPaymentById(int $paymentId): ?object
    {
        try {
            $client = new PaymentClient();
            return $client->get($paymentId);
        } catch (\Throwable $e) {
            Log::warning("MercadoPagoService: error obteniendo payment #{$paymentId}", [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Procesa el resultado de un pago para una reserva.
    // Idempotente: si ya existe un Payment con ese external_id, no hace nada.
    // Devuelve true si procesó, false si ya estaba procesado o no aplica.
    // ─────────────────────────────────────────────────────────────────────────

    public function processPayment(Booking $booking, Payment $successPayment)
    {
        $wasReactivated = false;

        DB::transaction(function () use ($booking, $successPayment, &$wasReactivated) {
            $booking->transaction->markPaymentAsPaid($successPayment->id);

            // Webhook duplicado: ya estaba confirmado o completado, no hay nada más que hacer.
            if ($booking->state instanceof BookingConfirmed || $booking->state instanceof BookingCompleted) {
                return;
            }

            // El turno ya se había cancelado/expirado (p. ej. por timeout de pago) cuando
            // llegó esta confirmación tardía de MercadoPago: el cliente sí pagó, así que
            // se reactiva el turno como confirmado en vez de perder el pago.
            $wasReactivated = $booking->state instanceof BookingCancelled || $booking->state instanceof BookingExpired;

            $booking->state->transitionTo(BookingConfirmed::class);
        });

        $booking->user->notify(
            new UserNotification(
                title: "Pago Aprobado",
                body: "Tu pago ha sido aprobado. El masajista será notificado y confirmará tu turno pronto."
            )
        );

        if ($wasReactivated) {
            $booking->therapist->user?->notify(
                new UserNotification(
                    title: 'Turno reactivado',
                    body: "El turno del {$booking->date} a las {$booking->start_time} se había cancelado por falta de pago, pero el cliente pagó y volvió a confirmarse.",
                )
            );
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Emite un reembolso total de un pago aprobado.
    // Se usa cuando el local/masajista cancela un turno ya pagado.
    // ─────────────────────────────────────────────────────────────────────────

    public function refund(string $mpPaymentId): bool
    {
        try {
            $client = new PaymentRefundClient();
            //$client->create((int) $mpPaymentId);

            return true;
        } catch (\Throwable $e) {
            Log::error("MercadoPagoService: error procesando reembolso del pago #{$mpPaymentId}", [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Crea una preference de Checkout Pro para el pago de una suscripción de local.
    // return ['init_point', 'preference_id']
    // ─────────────────────────────────────────────────────────────────────────

    public function createSubscriptionPreference(SubscriptionPayment $payment): array
    {
        MercadoPagoConfig::setAccessToken(env('MP_ACCESS_TOKEN'));

        $subscriptionId = $payment->subscription_id;

        $backUrls = [
            'success' => route('subscriptions.mp-return', ['status' => 'success', 'subscription_id' => $subscriptionId]),
            'failure' => route('subscriptions.mp-return', ['status' => 'failure', 'subscription_id' => $subscriptionId]),
            'pending' => route('subscriptions.mp-return', ['status' => 'pending', 'subscription_id' => $subscriptionId]),
        ];

        $payload = [
            'items' => [[
                'id'          => 'subscription_' . $subscriptionId,
                'title'       => 'Suscripción BodyFix — ' . $payment->period_start->format('m/Y'),
                'quantity'    => 1,
                'unit_price'  => (float) $payment->amount,
                'currency_id' => 'ARS',
            ]],
            'notification_url'   => route('subscriptions.webhook.mercado-pago'),
            'external_reference' => (string) $subscriptionId,
            'back_urls'          => $backUrls,
            'auto_return'        => 'approved',
        ];

        Log::debug('MercadoPagoService: payload para crear preference de suscripción', ['payload' => $payload]);

        try {
            $client     = new PreferenceClient();
            $preference = $client->create($payload);
        } catch (\MercadoPago\Exceptions\MPApiException $e) {
            $apiResponse = $e->getApiResponse();
            Log::error('MercadoPagoService: error creando preference de suscripción', [
                'status_code'   => $apiResponse?->getStatusCode(),
                'response_body' => $apiResponse?->getContent(),
                'payload_sent'  => $payload,
            ]);
            throw $e;
        }

        $isSandbox = config('mercadopago.sandbox');
        $url = $isSandbox ? $preference->sandbox_init_point : $preference->init_point;

        if (empty($url)) {
            $url = $preference->init_point ?? '';
        }

        return [
            'init_point'     => $url,
            'preference_id'  => $preference->id,
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Procesa el resultado de un pago aprobado para la suscripción de un local.
    // Activa la suscripción y actualiza el período vigente.
    // ─────────────────────────────────────────────────────────────────────────

    public function processSubscriptionPayment(Subscription $subscription, SubscriptionPayment $successPayment): void
    {
        DB::transaction(function () use ($subscription, $successPayment) {
            $successPayment->update([
                'status'  => PaymentStatus::APPROVED,
                'paid_at' => now(),
            ]);

            $subscription->update([
                'status'                => SubscriptionStatus::ACTIVE,
                'current_period_start'  => $successPayment->period_start,
                'current_period_end'    => $successPayment->period_end,
            ]);
        });

        $subscription->local->user?->notify(
            new UserNotification(
                title: 'Pago de suscripción aprobado',
                body: 'Tu suscripción fue activada correctamente. ¡Gracias por confiar en BodyFix!'
            )
        );
    }
}
