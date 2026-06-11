<?php

namespace App\Models\Therapists\States\Booking;

class BookingPendingPayment extends BookingState
{
    public static $name = 'pending_payment';

    public function label(): string
    {
        return __('booking.pending_payment.label');
    }

    public function description(): string
    {
        return __('booking.pending_payment.description');
    }
}
