<?php

namespace App\Filament\App\Concerns;

trait RequiresActiveSubscription
{
    /**
     * Un local suspendido no puede operar aunque tenga una suscripción activa.
     */
    protected static function subscriptionIsActive(): bool
    {
        $local = auth()->user()?->local;

        return $local !== null
            && ! $local->isSuspended()
            && $local->hasActiveSubscription();
    }
}
