<?php

namespace App\Core\forms;

use Filament\Forms;
use Filament\Forms\Form;
use Spatie\Tags\Tag;

trait HasAnnouncementForm
{
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Título')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label('Descripción')
                    ->required()
                    ->maxLength(65535),
                Forms\Components\TextInput::make('price')
                    ->label('Precio')
                    ->numeric()
                    ->required(),
                Forms\Components\Select::make('currency')
                    ->label('Moneda')
                    ->default('ARG')
                    ->options([
                        'ARG' => 'ARS - Peso Argentino',
                     ])
                    ->required(),
                    Forms\Components\Select::make('duration_in_minutes')
                    ->label('Duración (minutos)')
                    ->options([
                        30 => '30 minutos',
                        60 => '60 minutos',
                        90 => '90 minutos',
                    ])
                    ->required(),
                Forms\Components\Select::make('new_dicipline')
                    ->label('Disciplina')
                    ->options([
                        Tag::all()->pluck('name', 'name')->toArray(),
                    ])
                    ->dehydrated(false)
                    ->required(),
                
            ]);
    }
}