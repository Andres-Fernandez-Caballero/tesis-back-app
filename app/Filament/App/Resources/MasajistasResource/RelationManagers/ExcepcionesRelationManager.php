<?php

namespace App\Filament\App\Resources\MasajistasResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ExcepcionesRelationManager extends RelationManager
{
    protected static string $relationship = 'availabilyExceptions';

    protected static ?string $title = 'Excepciones de disponibilidad';

    protected static ?string $label = 'excepción';

    protected static ?string $pluralLabel = 'excepciones';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date')
                    ->label('Fecha')
                    ->required()
                    ->native(false)
                    ->columnSpanFull(),

                Forms\Components\Placeholder::make('date_range_info')
                    ->label('')
                    ->content('Si no se ingresa horario de inicio y fin, se considera el día completo.')
                    ->columnSpanFull(),

                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TimePicker::make('start_time')
                            ->label('Hora de inicio')
                            ->reactive()
                            ->requiredWith('end_time')
                            ->beforeOrEqual('end_time')
                            ->seconds(false)
                            ->format('H:i')
                            ->native(false),

                        Forms\Components\TimePicker::make('end_time')
                            ->label('Hora de fin')
                            ->reactive()
                            ->requiredWith('start_time')
                            ->afterOrEqual('start_time')
                            ->seconds(false)
                            ->format('H:i')
                            ->native(false),
                    ]),

                Forms\Components\Textarea::make('reason')
                    ->label('Motivo')
                    ->placeholder('Ej: Feriado, enfermedad, vacaciones...')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_time')
                    ->label('Desde')
                    ->time('H:i')
                    ->placeholder('Todo el día'),

                Tables\Columns\TextColumn::make('end_time')
                    ->label('Hasta')
                    ->time('H:i')
                    ->placeholder('Todo el día'),

                Tables\Columns\TextColumn::make('reason')
                    ->label('Motivo')
                    ->placeholder('—')
                    ->limit(50),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Agregar excepción'),
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
            ->defaultSort('date', 'asc');
    }
}
