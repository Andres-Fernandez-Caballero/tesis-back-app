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
            // ── Flujo con seña (nuevo) ────────────────────────────────────────
            // pending_payment → confirmed: pago aprobado (directo, sin step manual)
            ->allowTransition(BookingPendingPayment::class, BookingConfirmed::class)  // pago aprobado ✓
            ->allowTransition(BookingPendingPayment::class, BookingCancelled::class)  // pago rechazado/timeout
            ->allowTransition(BookingPendingPayment::class, BookingExpired::class)    // fecha pasó sin pagar
            // ── Compatibilidad retroactiva (datos históricos) ─────────────────
            // pending_payment → pending: flujo antiguo, no se usa en bookings nuevos
            ->allowTransition(BookingPendingPayment::class, BookingPending::class)
            ->allowTransition(BookingPending::class,   BookingConfirmed::class)
            ->allowTransition(BookingPending::class,   BookingCancelled::class)
            ->allowTransition(BookingPending::class,   BookingExpired::class)
            // ── Flujo post-confirmación ───────────────────────────────────────
            ->allowTransition(BookingConfirmed::class, BookingCompleted::class)
            ->allowTransition(BookingConfirmed::class, BookingCancelled::class)
            ->allowTransition(BookingConfirmed::class, BookingExpired::class);
    }
}