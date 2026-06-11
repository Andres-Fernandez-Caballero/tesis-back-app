<?php

namespace App\Filament\Admin\Resources\LocalResource\Pages;

use App\Filament\Admin\Resources\LocalResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewLocal extends ViewRecord
{
    protected static string $resource = LocalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('suspender')
                ->label('Suspender local')
                ->icon('heroicon-o-no-symbol')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status->value === 'active')
                ->action(function (): void {
                    $this->record->update(['status' => 'suspended']);
                    $this->refreshFormData(['status']);
                }),

            Actions\Action::make('activar')
                ->label('Reactivar local')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status->value === 'suspended')
                ->action(function (): void {
                    $this->record->update(['status' => 'active']);
                    $this->refreshFormData(['status']);
                }),
        ];
    }
}
