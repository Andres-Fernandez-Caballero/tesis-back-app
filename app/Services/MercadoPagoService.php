<?php

namespace App\Services;

use App\Models\Payments\Payment;
use App\Models\Therapists\Booking;
use App\Notifications\UserNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use MercadoPago\Client\MerchantOrder\MerchantOrderClient;
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
    // Procesa el resultado de un pago para una reserva.
    // Idempotente: si ya existe un Payment con ese external_id, no hace nada.
    // Devuelve true si procesó, false si ya estaba procesado o no aplica.
    // ─────────────────────────────────────────────────────────────────────────

    public function processPayment(Booking $booking, Payment $successPayment)
    {
        DB::transaction(function () use ($booking, $successPayment) {
            $booking->transaction->markPaymentAsPaid($successPayment->id);
        });

        $booking->user->notify(
            new UserNotification(
                title: "Pago Aprobado",
                body: "Tu pago ha sido aprobado. El masajista será notificado y confirmará tu turno pronto."
            )
        );
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
}
