<?php

namespace App\Core\UseCases\Payments;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Therapists\Booking;
use App\Services\MercadoPagoService;
use Illuminate\Support\Facades\Log;
use MercadoPago\Exceptions\MPApiException;

class MercadoPagoPayment extends AbstractPayment
{
    public function processPayment(Booking $booking, string $platform = 'web'): PaymentResult
    {
        $transaction = $booking->transaction;

        try {
            ['init_point' => $initPoint, 'preference_id' => $preferenceId] = app(MercadoPagoService::class)->createPreference($booking, $platform);

            $payment = $this->recordPayment(
                $transaction,
                PaymentMethod::MERCADO_PAGO,
                PaymentStatus::PENDING,
            );

            $payment->update([
                'preference_id' => $preferenceId,
            ]);

            return new PaymentResult(
                status: PaymentStatus::PENDING,
                transactionId: $transaction->id,
                payment_url: $initPoint,
            );
        } catch (MPApiException $e) {
            Log::error('Error de API al procesar pago con Mercado Pago', [
                'transaction_id' => $transaction->id,
                'http_status'    => $e->getStatusCode(),
                'api_response'   => $e->getApiResponse()->getContent(),
                'error_message'  => $e->getMessage(),
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error inesperado al procesar pago con Mercado Pago', [
                'transaction_id' => $transaction->id,
                'error_class'    => get_class($e),
                'error_message'  => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
