<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum LocalStatus: string implements HasLabel, HasColor
{
    case ACTIVE    = 'active';
    case SUSPENDED = 'suspended';

    public function getLabel(): ?string
    {
        return match($this) {
            self::ACTIVE    => 'Activo',
            self::SUSPENDED => 'Suspendido',
        };
    }

    public function getColor(): string|array|null
    {
        return match($this) {
            self::ACTIVE    => 'success',
            self::SUSPENDED => 'danger',
        };
    }
}
