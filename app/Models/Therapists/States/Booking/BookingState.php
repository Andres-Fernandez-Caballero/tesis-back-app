<?php

namespace App\Models\Therapists\States\Booking;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class BookingState extends State
{
    abstract public function label(): string;
    abstract public function description(): string;    

    public static function config(): StateConfig
    {
        return parent::config()
            ->default(BookingPending::class)
            ->allowTransition(BookingPending::class, BookingConfirmed::class)
            ->allowTransition(BookingConfirmed::class, BookingCompleted::class)
            ->allowTransition(BookingPending::class, BookingCancelled::class)
            ->allowTransition(BookingConfirmed::class, BookingCancelled::class);
    }
}