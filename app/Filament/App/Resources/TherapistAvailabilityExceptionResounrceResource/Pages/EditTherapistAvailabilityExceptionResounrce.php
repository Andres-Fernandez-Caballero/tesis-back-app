<?php

namespace App\Filament\App\Resources\TherapistAvailabilityExceptionResounrceResource\Pages;

use App\Filament\App\Resources\TherapistAvailabilityExceptionResounrceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTherapistAvailabilityExceptionResounrce extends EditRecord
{
    protected static string $resource = TherapistAvailabilityExceptionResounrceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
