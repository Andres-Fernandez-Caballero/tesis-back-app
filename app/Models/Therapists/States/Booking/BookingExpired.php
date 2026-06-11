<?php

namespace App\Models\Therapists\States\Booking;

class BookingExpired extends BookingState
{
    public static $name = 'expired';

    public function label(): string
    {
        return __('booking.expired.label');
    }

    public function description(): string
    {
        return __('booking.expired.description');
    }
}
