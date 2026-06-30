<?php

namespace App\Filament\App\Resources\MasajistasResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class DisponibilidadRelationManager extends RelationManager
{
    protected static string $relationship = 'availabilities';

    protected static ?string $title = 'Disponibilidad horaria';

    protected static ?string $label = 'franja horaria';

    protected static ?string $pluralLabel = 'franjas horarias';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\CheckboxList::make('day_of_week')
                    ->label('Días de la semana')
                    ->options([
                        1 => 'Lunes',
                        2 => 'Martes',
                        3 => 'Miércoles',
                        4 => 'Jueves',
                        5 => 'Viernes',
                        6 => 'Sábado',
                        7 => 'Domingo',
                    ])
                    ->required()
                    ->columns(4)
                    ->gridDirection('row')
                    ->columnSpanFull(),

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

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('day_of_week')
                    ->label('Días')
                    ->state(function ($record): string {
                        $nombres = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
                        $days = is_array($record->day_of_week)
                            ? $record->day_of_week
                            : json_decode($record->getRawOriginal('day_of_week') ?? '[]', true);
                        return collect($days)
                            ->map(fn ($d) => $nombres[(int) $d] ?? null)
                            ->filter()
                            ->implode(', ') ?: '—';
                    })
                    ->wrap(),

                Tables\Columns\TextColumn::make('start_time')
                    ->label('Desde')
                    ->time('H:i'),

                Tables\Columns\TextColumn::make('end_time')
                    ->label('Hasta')
                    ->time('H:i'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Agregar franja'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('day_of_week');
    }
}
