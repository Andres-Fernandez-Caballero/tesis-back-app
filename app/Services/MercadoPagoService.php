<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Enums\TransactionStatus;
use App\Models\Payments\Payment;
use App\Models\Therapists\Booking;
use App\Models\Therapists\States\Booking\BookingCancelled;
use App\Models\Therapists\States\Booking\BookingConfirmed;
use App\Models\Therapists\States\Booking\BookingPending;
use App\Notifications\UserNotification;
use Illuminate\Support\Facades\Log;
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
    // Crea una preference de Checkout Pro y devuelve la URL de pago.
    // ─────────────────────────────────────────────────────────────────────────

    public function createPreference(Booking $booking): string
    {
        $booking->loadMissing('especialidad');

        $title = $booking->especialidad
            ? "Seña — {$booking->especialidad->nombre}"
            : 'Seña — BodyFix';

        // back_urls apuntan al deep link de la app mobile.
        // bodyfix:// es el scheme registrado en app.json — MP lo acepta como back_url
        // para apps mobile y funciona tanto en Expo Go (que registra el scheme) como
        // en builds standalone. El browser in-app (openAuthSessionAsync) detecta
        // el redirect a este scheme y cierra el checkout automáticamente.
        $mobileScheme = config('mercadopago.mobile_scheme', 'bodyfix');
        $payload = [
            'items' => [[
                'id'          => 'booking_' . $booking->id,
                'title'       => $title,
                'quantity'    => 1,
                'unit_price'  => (float) $booking->price,
                'currency_id' => 'ARS',
            ]],
            'external_reference' => (string) $booking->id,
            'back_urls' => [
                'success' => "{$mobileScheme}://payment-callback?status=success&booking_id={$booking->id}",
                'failure' => "{$mobileScheme}://payment-callback?status=failure&booking_id={$booking->id}",
                'pending' => "{$mobileScheme}://payment-callback?status=pending&booking_id={$booking->id}",
            ],
            'auto_return' => 'approved',
        ];

        // notification_url solo cuando el servidor tiene una URL pública HTTPS
        // (no aplica en desarrollo local con IP privada).
        $appUrl = rtrim(config('app.url'), '/');
        $isPublicUrl = str_starts_with($appUrl, 'https://') && ! filter_var(
            parse_url($appUrl, PHP_URL_HOST),
            FILTER_VALIDATE_IP
        );

        if ($isPublicUrl) {
            $payload['notification_url'] = "{$appUrl}/api/v1/payments/webhook/mercado-pago";
        }

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

        return $url;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Consulta MP directamente para obtener el pago más reciente
    // asociado a una reserva (por external_reference = booking_id).
    // Se usa para hacer polling cuando no hay webhook disponible.
    // ─────────────────────────────────────────────────────────────────────────

    public function getLatestPaymentByExternalRef(int $bookingId): ?object
    {
        try {
            $client  = new PaymentClient();
            $results = $client->search([
                'external_reference' => (string) $bookingId,
                'sort'               => 'date_created',
                'criteria'           => 'desc',
                'limit'              => 1,
            ]);

            if (! empty($results->results)) {
                return $results->results[0];
            }
        } catch (\Throwable $e) {
            Log::warning("MercadoPagoService: error buscando pago para booking #{$bookingId}", [
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Obtiene un pago de MP por su ID.
    // ─────────────────────────────────────────────────────────────────────────

    public function getPaymentById(int $mpPaymentId): ?object
    {
        try {
            $client = new PaymentClient();
            return $client->get($mpPaymentId);
        } catch (\Throwable $e) {
            Log::warning("MercadoPagoService: error obteniendo pago #{$mpPaymentId}", [
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

    public function processPayment(Booking $booking, object $mpPayment): bool
    {
        $mpPaymentId = (string) $mpPayment->id;

        // Idempotencia: si ya procesamos este pago, no lo procesar dos veces
        if (Payment::where('external_id', $mpPaymentId)->exists()) {
            return false;
        }

        $booking->loadMissing('transaction', 'user');

        if (! $booking->transaction) {
            Log::error("MercadoPagoService: booking #{$booking->id} no tiene transacción asociada.");
            return false;
        }

        $status     = $mpPayment->status ?? 'pending';
        $statusMap  = [
            'approved'          => PaymentStatus::APPROVED,
            'rejected'          => PaymentStatus::FAILED,
            'cancelled'         => PaymentStatus::FAILED,
            'in_process'        => PaymentStatus::PROCESSING,
            'pending'           => PaymentStatus::PENDING,
            'authorized'        => PaymentStatus::APPROVED,
            'charge_back'       => PaymentStatus::REFUNDED,
        ];

        $paymentStatus = $statusMap[$status] ?? PaymentStatus::PENDING;

        // Crear registro de Payment
        $booking->transaction->payments()->create([
            'user_id'        => $booking->user_id,
            'amount'         => $mpPayment->transaction_amount ?? $booking->price,
            'currency'       => 'ARS',
            'payment_status' => $paymentStatus,
            'payment_method' => 'mercado_pago',
            'external_id'    => $mpPaymentId,
            'preference_id'  => $mpPayment->order->id ?? null,
            'payment_data'   => (array) $mpPayment,
            'paid_at'        => in_array($paymentStatus, [PaymentStatus::APPROVED])
                ? now()
                : null,
        ]);

        if (in_array($status, ['approved', 'authorized'])) {
            // Pago aprobado: el turno queda confirmado directamente (sin step manual)
            $booking->state->transitionTo(BookingConfirmed::class);
            $booking->transaction->update(['status' => TransactionStatus::COMPLETED]);

            if ($booking->user) {
                $hora  = substr($booking->start_time ?? '', 0, 5);
                $fecha = $booking->date;
                $booking->user->notify(new UserNotification(
                    title: '¡Turno confirmado! ✓',
                    body:  "Tu pago fue aprobado y tu turno del {$fecha} a las {$hora} quedó confirmado.",
                ));
            }

            return true;
        }

        if (in_array($status, ['rejected', 'cancelled'])) {
            // Rechazar: booking pasa a cancelled
            $booking->state->transitionTo(BookingCancelled::class);
            $booking->transaction->update(['status' => TransactionStatus::FAILED]);

            if ($booking->user) {
                $booking->user->notify(new UserNotification(
                    title: 'Pago rechazado',
                    body:  'Tu pago no pudo procesarse. La reserva fue cancelada. Podés intentarlo de nuevo.',
                ));
            }

            return true;
        }

        return false;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Emite un reembolso total de un pago aprobado.
    // Se usa cuando el local/masajista cancela un turno ya pagado.
    // ─────────────────────────────────────────────────────────────────────────

    public function refund(string $mpPaymentId): bool
    {
        try {
            $client = new PaymentRefundClient();
            $client->create((int) $mpPaymentId);

            return true;
        } catch (\Throwable $e) {
            Log::error("MercadoPagoService: error procesando reembolso del pago #{$mpPaymentId}", [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
