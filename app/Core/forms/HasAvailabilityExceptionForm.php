<?php

namespace App\Core\forms;

use Filament\Forms;
use Filament\Forms\Form;


trait HasAvailabilityExceptionForm
{
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date')->required(),
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\Placeholder::make('date_range_info')
                            ->content(fn() => 'ℹ️ Si no selecciona tiempos de inicio y fin, se considera todo el día.')
                            ->columnSpanFull(),
                        Forms\Components\TimePicker::make('start_time')
                            ->reactive()
                            ->requiredWith('end_time')
                            ->beforeOrEqual('end_time')
                            ->closeOnDateSelection()
                            ->seconds(false)
                            ->format('H:i')
                            ->native(false)
                            ->label('Start time'),

                        Forms\Components\TimePicker::make('end_time')
                            ->reactive()
                            ->requiredWith('start_time')
                            ->afterOrEqual('start_time')
                            ->closeOnDateSelection()
                            ->seconds(false)
                            ->format('H:i')
                            ->native(false)
                            ->label('End time'),
                    ]),
                Forms\Components\Textarea::make('reason'),
            ]);
    }
}
