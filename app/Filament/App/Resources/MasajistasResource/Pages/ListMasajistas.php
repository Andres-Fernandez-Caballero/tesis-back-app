<?php

namespace App\Filament\App\Resources\MasajistasResource\Pages;

use App\Filament\App\Resources\MasajistasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMasajistas extends ListRecords
{
    protected static string $resource = MasajistasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Agregar masajista'),
        ];
    }
}
