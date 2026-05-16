<?php

namespace App\Core\UseCases\Payments;

use App\Models\Payments\Transaction;
use App\Models\User;

interface Paymentable
{
    // Define los métodos que deben implementarse para procesar un pago
    public function processPayment(
        Transaction $transaction,
    ): PaymentResult;
}
