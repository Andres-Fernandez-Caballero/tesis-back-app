<?php

namespace App\Models\Therapists\States\Booking;

class BookingCancelled extends BookingState
{
    public static $name = 'cancelled';
    
    public function label(): string
    {
        return __('booking.cancelled.label');
    }

    public function description(): string
    {
        return __('booking.cancelled.description');
    }
}