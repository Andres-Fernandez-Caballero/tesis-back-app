<?php

namespace App\Models\Therapists\States\Booking;

class BookingConfirmed extends BookingState
{
    public static $name = 'confirmed';
    
    public function label(): string
    {
        return __('booking.confirmed.label');
    }

    public function description(): string
    {
        return __('booking.confirmed.description');
    }
}