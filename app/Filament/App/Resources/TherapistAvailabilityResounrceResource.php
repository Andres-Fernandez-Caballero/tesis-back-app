<?php

namespace App\Filament\App\Resources;

use App\Core\forms\HasAvailabilityForm;
use App\Filament\App\Resources\TherapistAvailabilityResounrceResource\Pages;
use App\Filament\App\Resources\TherapistAvailabilityResounrceResource\RelationManagers;
use App\Models\TherapistAvailabilityResounrce;
use App\Models\Therapists\Availability;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TherapistAvailabilityResounrceResource extends Resource
{
    protected static ?string $model = Availability::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    use HasAvailabilityForm;

    public static function table(Table $table): Table
    {
        return $table
            ->query(Availability::where('therapist_id', auth()->user()->therapist->id))
            ->columns([
                Tables\Columns\TextColumn::make('day_of_week')
                ->formatStateUsing(fn($state) => match($state) {
                    1 => 'Lunes',
                    2 => 'Martes',
                    3 => 'Miercoles',
                    4 => 'Jueves',
                    5 => 'Viernes',
                    6 => 'Sabado',
                    7 => 'Domingo',
                }),
                Tables\Columns\TextColumn::make('start_time')->time('H:i'),
                Tables\Columns\TextColumn::make('end_time')->time('H:i'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTherapistAvailabilityResounrces::route('/'),
            'create' => Pages\CreateTherapistAvailabilityResounrce::route('/create'),
            'edit' => Pages\EditTherapistAvailabilityResounrce::route('/{record}/edit'),
        ];
    }
}
