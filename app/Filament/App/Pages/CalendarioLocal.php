<?php

namespace App\Filament\App\Pages;

use App\Enums\Role;
use Filament\Pages\Page;

class CalendarioLocal extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Calendario';
    protected static ?string $title           = 'Calendario de turnos';
    protected static ?int    $navigationSort  = 0;

    protected static string $view = 'filament.app.pages.calendario-local';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole(Role::SPA_OWNER) ?? false;
    }
}
