<?php

namespace App\Models\Therapists\States\Booking;

class BookingCompleted extends BookingState
{
    public static $name = 'completed';
    
    public function label(): string
    {
        return __('booking.completed.label');
    }

    public function description(): string
    {
        return __('booking.completed.description');
    }
}