<?php

namespace App\Filament\App\Resources\MisTurnosResource\Pages;

use App\Filament\App\Resources\MisTurnosResource;
use Filament\Resources\Pages\ListRecords;

class ListMisTurnos extends ListRecords
{
    protected static string $resource = MisTurnosResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
