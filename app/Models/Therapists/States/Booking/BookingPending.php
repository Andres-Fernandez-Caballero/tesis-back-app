<?php

namespace App\Models\Therapists\States\Booking;

class BookingPending extends BookingState
{
    public static $name = 'pending';

    public function label(): string
    {
        return __('booking.pending.label');
    }

    public function description(): string
    {
        return __('booking.pending.description');
    }
}