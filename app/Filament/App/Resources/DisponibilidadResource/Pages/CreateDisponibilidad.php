<?php

namespace App\Filament\App\Resources\DisponibilidadResource\Pages;

use App\Filament\App\Resources\DisponibilidadResource;
use App\Models\Therapists\Availability;
use App\Models\Therapists\MassageTherapist;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class CreateDisponibilidad extends CreateRecord
{
    protected static string $resource = DisponibilidadResource::class;

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('therapist_id')
                ->label('Masajista')
                ->options(function (): array {
                    return MassageTherapist::where('local_id', auth()->user()?->local?->id)
                        ->where('activo', true)
                        ->orderBy('nombre')
                        ->pluck('nombre', 'id')
                        ->toArray();
                })
                ->required()
                ->searchable(),

            Forms\Components\CheckboxList::make('day_of_week')
                ->label('Días de la semana')
                ->options(DisponibilidadResource::diasOptions())
                ->required()
                ->columns(4)
                ->gridDirection('row'),

            Forms\Components\Grid::make(2)
                ->schema([
                    Forms\Components\TimePicker::make('start_time')
                        ->label('Hora de inicio')
                        ->required()
                        ->seconds(false)
                        ->format('H:i')
                        ->native(false),

                    Forms\Components\TimePicker::make('end_time')
                        ->label('Hora de fin')
                        ->required()
                        ->seconds(false)
                        ->format('H:i')
                        ->native(false)
                        ->after('start_time'),
                ]),
        ]);
    }

    protected function handleRecordCreation(array $data): Model
    {
        $days = (array) $data['day_of_week'];
        $diasOptions = DisponibilidadResource::diasOptions();

        foreach ($days as $day) {
            $hasOverlap = Availability::where('therapist_id', $data['therapist_id'])
                ->whereJsonContains('day_of_week', (int) $day)
                ->where('start_time', '<', $data['end_time'])
                ->where('end_time', '>', $data['start_time'])
                ->exists();

            if ($hasOverlap) {
                $dayName = $diasOptions[$day] ?? $day;
                throw ValidationException::withMessages([
                    'data.day_of_week' => "El masajista ya tiene una franja que se superpone el {$dayName}.",
                ]);
            }
        }

        return Availability::create([
            'therapist_id' => $data['therapist_id'],
            'day_of_week'  => $days,
            'start_time'   => $data['start_time'],
            'end_time'     => $data['end_time'],
        ]);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
