<?php

namespace App\Filament\App\Resources\TherapistAvailabilityResounrceResource\Pages;

use App\Filament\App\Resources\TherapistAvailabilityResounrceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTherapistAvailabilityResounrce extends EditRecord
{
    protected static string $resource = TherapistAvailabilityResounrceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
