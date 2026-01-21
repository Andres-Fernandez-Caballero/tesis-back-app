<?php

namespace App\Filament\App\Resources\TherapistAvailabilityResounrceResource\Pages;

use App\Filament\App\Resources\TherapistAvailabilityResounrceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTherapistAvailabilityResounrces extends ListRecords
{
    protected static string $resource = TherapistAvailabilityResounrceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
