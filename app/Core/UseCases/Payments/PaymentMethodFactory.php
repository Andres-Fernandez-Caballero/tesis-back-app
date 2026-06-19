<?php

namespace App\Core\UseCases\Payments;

use App\Enums\PaymentMethod;

class PaymentMethodFactory
{
    public static function create(string $method): Paymentable
    {
        return match ($method) {
            'fake' => new FakePayment(),
            PaymentMethod::MERCADO_PAGO->value => new MercadoPagoPayment(),
            // PaymentMethod::CREDIT_CARD->value => new CreditCardPayment(),

            default => throw new \InvalidArgumentException("Método de pago no soportado: {$method}"),
        };
    }
}
