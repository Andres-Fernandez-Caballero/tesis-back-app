<?php

namespace App\Filament\App\Resources;

use App\Core\forms\CreateAnnouncementForm;
use App\Filament\App\Resources\TherapistAnnouncementsResource\Pages;
use App\Filament\App\Resources\TherapistAnnouncementsResource\RelationManagers;
use App\Models\TherapistAnnouncements;
use App\Models\Therapists\Announcement;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TherapistAnnouncementsResource extends Resource
{
    use CreateAnnouncementForm;

    protected static ?string $model = Announcement::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModelLabel(): string
{
    return __('announcement.label');
}
    
    
    public static function table(Table $table): Table
    {
        return $table
            ->query(function (Builder $query) {
                return auth()->user()->therapist?->announcements(); 
            })
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Título')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Creado el')->dateTime('d/m/Y H:i')->sortable(),
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
            'index' => Pages\ListTherapistAnnouncements::route('/'),
            'create' => Pages\CreateTherapistAnnouncements::route('/create'),
            'edit' => Pages\EditTherapistAnnouncements::route('/{record}/edit'),
        ];
    }
}
