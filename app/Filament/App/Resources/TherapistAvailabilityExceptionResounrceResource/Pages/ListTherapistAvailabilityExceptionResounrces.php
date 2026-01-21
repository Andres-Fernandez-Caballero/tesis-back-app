<?php

namespace App\Filament\App\Resources\TherapistAvailabilityExceptionResounrceResource\Pages;

use App\Filament\App\Resources\TherapistAvailabilityExceptionResounrceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTherapistAvailabilityExceptionResounrces extends ListRecords
{
    protected static string $resource = TherapistAvailabilityExceptionResounrceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
