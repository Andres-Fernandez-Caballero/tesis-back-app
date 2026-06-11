<?php

namespace App\Filament\App\Resources\MasajistasResource\Pages;

use App\Filament\App\Resources\MasajistasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMasajista extends EditRecord
{
    protected static string $resource = MasajistasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label('Eliminar masajista'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
