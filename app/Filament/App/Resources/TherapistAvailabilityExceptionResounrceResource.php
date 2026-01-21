<?php

namespace App\Filament\App\Resources;

use App\Core\forms\HasAvailabilityExceptionForm;
use App\Filament\App\Resources\TherapistAvailabilityExceptionResounrceResource\Pages;
use App\Filament\App\Resources\TherapistAvailabilityExceptionResounrceResource\RelationManagers;
use App\Models\TherapistAvailabilityExceptionResounrce;
use App\Models\Therapists\AvailabilityException;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TherapistAvailabilityExceptionResounrceResource extends Resource
{
    protected static ?string $model = AvailabilityException::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    use HasAvailabilityExceptionForm;


    public static function table(Table $table): Table
    {
        return $table
            ->query(AvailabilityException::where('therapist_id',auth()->user()->therapist->id))
            ->columns([
                Tables\Columns\TextColumn::make('date')->date(),
                Tables\Columns\TextColumn::make('start_time')->time(),
                Tables\Columns\TextColumn::make('end_time')->time(),
                Tables\Columns\TextColumn::make('reason'),
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
            'index' => Pages\ListTherapistAvailabilityExceptionResounrces::route('/'),
            'create' => Pages\CreateTherapistAvailabilityExceptionResounrce::route('/create'),
            'edit' => Pages\EditTherapistAvailabilityExceptionResounrce::route('/{record}/edit'),
        ];
    }
}
