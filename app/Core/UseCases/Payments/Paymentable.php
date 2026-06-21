<?php

namespace App\Core\UseCases\Payments;

use App\Models\Therapists\Booking;
use App\Models\User;

interface Paymentable
{
    // Define los métodos que deben implementarse para procesar un pago
    public function processPayment(
        Booking $booking,
        string $platform = 'web',
    ): PaymentResult;
}
