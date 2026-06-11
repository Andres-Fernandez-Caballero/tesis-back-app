<?php

namespace App\Filament\App\Resources;

use App\Enums\Role;
use App\Filament\App\Resources\ReseniasResource\Pages;
use App\Models\Review;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ReseniasResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon  = 'heroicon-o-star';
    protected static ?string $navigationLabel = 'Reseñas';
    protected static ?string $pluralModelLabel = 'Reseñas';
    protected static ?string $modelLabel      = 'Reseña';
    protected static ?int    $navigationSort  = 3;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole(Role::SPA_OWNER) ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        $localId = auth()->user()?->local?->id;

        return parent::getEloquentQuery()
            ->with(['user', 'therapist', 'booking'])
            ->when($localId,  fn (Builder $q) => $q->where('local_id', $localId))
            ->when(! $localId, fn (Builder $q) => $q->whereRaw('0 = 1'));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('cliente')
                    ->label('Cliente')
                    ->state(fn (Review $record): string =>
                        trim(($record->user?->name ?? '') . ' ' . ($record->user?->last_name ?? '')) ?: '—')
                    ->searchable(query: fn (Builder $query, string $search) =>
                        $query->whereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%")))
                    ->sortable(query: fn (Builder $query, string $direction) =>
                        $query->join('users', 'reviews.user_id', '=', 'users.id')
                              ->orderBy('users.name', $direction)),

                TextColumn::make('masajista')
                    ->label('Masajista')
                    ->state(fn (Review $record): string =>
                        $record->therapist
                            ? trim(($record->therapist->nombre ?? '') . ' ' . ($record->therapist->apellido ?? ''))
                            : '—')
                    ->placeholder('—'),

                TextColumn::make('booking.date')
                    ->label('Fecha del turno')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('local_score')
                    ->label('Local')
                    ->formatStateUsing(fn (int $state): string =>
                        str_repeat('★', $state) . str_repeat('☆', 5 - $state))
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 4 => 'success',
                        $state >= 3 => 'warning',
                        default     => 'danger',
                    }),

                TextColumn::make('therapist_score')
                    ->label('Masajista')
                    ->formatStateUsing(fn (?int $state): string =>
                        $state !== null
                            ? str_repeat('★', $state) . str_repeat('☆', 5 - $state)
                            : '—')
                    ->placeholder('—'),

                TextColumn::make('comment')
                    ->label('Comentario')
                    ->limit(80)
                    ->placeholder('Sin comentario')
                    ->tooltip(fn (Review $record): ?string => $record->comment),

                TextColumn::make('created_at')
                    ->label('Fecha de reseña')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([])
            ->actions([])
            ->bulkActions([])
            ->emptyStateHeading('Sin reseñas aún')
            ->emptyStateDescription('Las calificaciones de tus clientes aparecerán aquí.')
            ->emptyStateIcon('heroicon-o-star');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListResenias::route('/'),
        ];
    }
}
