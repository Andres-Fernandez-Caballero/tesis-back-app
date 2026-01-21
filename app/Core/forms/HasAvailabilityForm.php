<?php

namespace App\Core\forms;

use Filament\Forms;
use Filament\Forms\Form;


trait HasAvailabilityForm
{
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('day_of_week')
                    ->options([
                        1 => 'Lunes',
                        2 => 'Martes',
                        3 => 'Miercoles',
                        4 => 'Jueves',
                        5 => 'Viernes',
                        6 => 'Sabado',
                        7 => 'Domingo',
                    ])
                    ->columnSpanFull()
                    ->required(),
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\TimePicker::make('start_time')
                            ->required()
                            ->beforeOrEqual('end_time')
                            ->closeOnDateSelection()
                            ->seconds(false)
                            ->format('H:i')
                            ->native(false)
                            ->label('Start time'),

                        Forms\Components\TimePicker::make('end_time')
                            ->required()
                            ->afterOrEqual('start_time')
                            ->closeOnDateSelection()
                            ->seconds(false)
                            ->format('H:i')
                            ->native(false)
                            ->label('End time'),
                    ]),
            ]);
    }
}
