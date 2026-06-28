<?php

namespace App\Core\UseCases\Payments;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\TransactionStatus;
use App\Models\Therapists\Booking;
use App\Models\Therapists\States\Booking\BookingConfirmed;

class FakePayment extends AbstractPayment
{
    public function processPayment(Booking $booking, string $platform = 'web'): PaymentResult
    {
        $transaction = $booking->transaction;

        $this->recordPayment(
            $transaction,
            PaymentMethod::FAKE,
            PaymentStatus::APPROVED,
            externalId: 'fake_' . uniqid(),
        );

        $transaction->update(['status' => TransactionStatus::COMPLETED]);
        $booking->state->transitionTo(BookingConfirmed::class);
        return new PaymentResult(
            PaymentStatus::APPROVED,
            transactionId: $transaction->id,
        );
    }
}
