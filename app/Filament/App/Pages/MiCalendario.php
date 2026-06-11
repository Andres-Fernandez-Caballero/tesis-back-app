<?php

namespace App\Filament\App\Pages;

use App\Enums\Role;
use Filament\Pages\Page;

class MiCalendario extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'Mi calendario';
    protected static ?string $title           = 'Mi calendario de turnos';
    protected static ?int    $navigationSort  = 0;

    protected static string $view = 'filament.app.pages.mi-calendario';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole(Role::MASSAGE_THERAPIST) ?? false;
    }
}
