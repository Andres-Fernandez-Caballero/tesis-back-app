<?php

namespace App\Filament\App\Resources\SolicitudesPendientesResource\Pages;

use App\Filament\App\Resources\SolicitudesPendientesResource;
use Filament\Resources\Pages\ListRecords;

class ListSolicitudesPendientes extends ListRecords
{
    protected static string $resource = SolicitudesPendientesResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
