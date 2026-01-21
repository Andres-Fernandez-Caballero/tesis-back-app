<?php

namespace App\Filament\App\Resources\TherapistAvailabilityExceptionResounrceResource\Pages;

use App\Filament\App\Resources\TherapistAvailabilityExceptionResounrceResource;
use App\Models\Therapists\AvailabilityException;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTherapistAvailabilityExceptionResounrce extends CreateRecord
{
    protected static string $resource = TherapistAvailabilityExceptionResounrceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['therapist_id'] = auth()->user()->therapist->id;
        return $data;
    }
}
