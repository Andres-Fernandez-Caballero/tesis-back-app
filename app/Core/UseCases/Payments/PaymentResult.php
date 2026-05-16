<?php

namespace App\Core\UseCases\Payments;

use App\Enums\PaymentStatus;

class PaymentResult
{
    public function __construct(
        public PaymentStatus $status,
        public ?string $transactionId = null,
        public ?string $errorMessage = null,
        public ?string $payment_url = null
    ) {}
}
