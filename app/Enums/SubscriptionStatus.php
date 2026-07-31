<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum SubscriptionStatus: string implements HasLabel, HasColor
{
    case PENDING_PAYMENT = 'pending_payment';
    case ACTIVE          = 'active';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PENDING_PAYMENT => 'Pago pendiente',
            self::ACTIVE          => 'Activa',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::PENDING_PAYMENT => 'danger',
            self::ACTIVE          => 'success',
        };
    }
}
