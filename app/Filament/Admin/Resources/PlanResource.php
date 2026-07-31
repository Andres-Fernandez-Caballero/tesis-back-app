<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PlanResource\Pages;
use App\Models\Subscriptions\Plan;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationLabel = 'Planes';

    protected static ?string $navigationGroup = 'Locales';

    protected static ?string $pluralModelLabel = 'Planes';

    protected static ?string $modelLabel = 'Plan';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Plan')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextInputColumn::make('price')
                    ->label('Precio')
                    ->type('number')
                    ->step('0.01')
                    ->rules(['required', 'numeric', 'min:0'])
                    ->afterStateUpdated(function (Plan $record): void {
                        Notification::make()
                            ->title('Precio actualizado')
                            ->body("El plan \"{$record->name}\" ahora cuesta {$record->price} {$record->currency}.")
                            ->success()
                            ->send();
                    }),

                Tables\Columns\TextColumn::make('currency')
                    ->label('Moneda'),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Activo')
                    ->afterStateUpdated(function (Plan $record): void {
                        Notification::make()
                            ->title($record->is_active ? 'Plan activado' : 'Plan desactivado')
                            ->success()
                            ->send();
                    }),

                Tables\Columns\TextColumn::make('subscriptions_count')
                    ->label('Suscripciones')
                    ->getStateUsing(fn (Plan $record): int => $record->subscriptions()->count())
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Última actualización')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->bulkActions([])
            ->defaultSort('name', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlans::route('/'),
        ];
    }
}
