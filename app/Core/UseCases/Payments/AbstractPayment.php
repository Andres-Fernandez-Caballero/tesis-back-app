<?php

namespace App\Core\UseCases\Payments;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Payments\Payment;
use App\Models\Payments\Transaction;

abstract class AbstractPayment implements Paymentable
{
    protected function recordPayment(
        Transaction $transaction,
        PaymentMethod $method,
        PaymentStatus $status,
        ?string $externalId = null,
        ?string $preferenceId = null,
    ): Payment {
        $transaction->payments()
            ->where('payment_status', PaymentStatus::PENDING)
            ->update(['payment_status' => PaymentStatus::FAILED]);

        return $transaction->payments()->create([
            'user_id'        => $transaction->client_id,
            'amount'         => $transaction->amount,
            'currency'       => $transaction->currency,
            'payment_status' => $status,
            'payment_method' => $method,
            'external_id'    => $externalId,
            'preference_id'  => $preferenceId,
            'paid_at'        => $status === PaymentStatus::APPROVED ? now() : null,
        ]);
    }
}
