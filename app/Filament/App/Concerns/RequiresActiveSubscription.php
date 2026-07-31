<?php

namespace App\Filament\App\Concerns;

trait RequiresActiveSubscription
{
    protected static function subscriptionIsActive(): bool
    {
        return auth()->user()?->local?->hasActiveSubscription() ?? false;
    }
}
