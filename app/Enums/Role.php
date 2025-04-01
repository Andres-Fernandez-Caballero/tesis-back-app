<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Role:string implements HasLabel
{
    case ADMIN = 'admin';
    case CLIENT = 'client';
    case MASSAGE_THERAPIST ='massage_therapist';

    public function getLabel(): ?string
    {
        return $this->name;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return array_map(fn ($case) => $case->getLabel(), self::cases());
    }

    public static function asArray(): array
    {
        return array_combine(self::values(), self::labels());
    }
}