<?php

namespace App\Filament\App\Pages;

use App\Enums\Role;
use Filament\Pages\Page;

class CuentaSuspendida extends Page
{
    protected static ?string $title = 'Cuenta suspendida';
    protected static string  $view  = 'filament.app.pages.cuenta-suspendida';

    // No se muestra en el menú: se llega acá solo por redirección forzada.
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return ($user?->hasRole(Role::SPA_OWNER) ?? false)
            || ($user?->hasRole(Role::MASSAGE_THERAPIST) ?? false);
    }
}
