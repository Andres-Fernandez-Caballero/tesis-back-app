<?php

namespace App\Core\UseCases\Payments;

use App\Enums\PaymentStatus;
use App\Enums\TransactionStatus;
use App\Models\Payments\Transaction;
use Illuminate\Support\Facades\Log;

class FakePayment implements Paymentable
{
    public function processPayment(
        Transaction $transaction,
    ): PaymentResult {

        $transaction->payments()
            ->where('payment_status', 'pending')
            ->update(['payment_status' => 'cancelled']);

        Log::info("transaccion", $transaction->toArray());

        $payment = $transaction->payments()->create([
            'user_id' => $transaction->client_id,
            'amount' => $transaction->amount,
            'currency' => $transaction->currency,
            'payment_status' => PaymentStatus::APPROVED,
            'payment_method' => 'fake',
            'external_id' => 'fake_' . uniqid(),
        ]);
        Log::info("payment", $payment->toArray());
        $transaction->update(['status' => TransactionStatus::COMPLETED]);
        // Simula un procesamiento de pago exitoso
        return new PaymentResult(
            PaymentStatus::APPROVED,
            transactionId: $transaction->id,
        );
    }
}
