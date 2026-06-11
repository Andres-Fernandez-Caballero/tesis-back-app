<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum LocalRegistrationStatus: string implements HasLabel, HasColor
{
    case PENDING  = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    public function getLabel(): ?string
    {
        return match($this) {
            self::PENDING  => 'Pendiente',
            self::APPROVED => 'Aprobado',
            self::REJECTED => 'Rechazado',
        };
    }

    public function getColor(): string|array|null
    {
        return match($this) {
            self::PENDING  => 'warning',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
        };
    }
}
