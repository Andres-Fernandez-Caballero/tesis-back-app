<?php

namespace App\Filament\App\Resources\TherapistAvailabilityResounrceResource\Pages;

use App\Filament\App\Resources\TherapistAvailabilityResounrceResource;
use App\Models\Therapists\Availability;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTherapistAvailabilityResounrce extends CreateRecord
{
    protected static string $resource = TherapistAvailabilityResounrceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['therapist_id'] = auth()->user()->therapist->id;
        return $data;
    }
}
