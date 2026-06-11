<?php

namespace App\Filament\Admin\Resources;

use App\Enums\LocalStatus;
use App\Filament\Admin\Resources\LocalResource\Pages;
use App\Models\Local;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LocalResource extends Resource
{
    protected static ?string $model = Local::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationLabel = 'Locales';

    protected static ?string $navigationGroup = 'Locales';

    protected static ?string $pluralModelLabel = 'Locales';

    protected static ?string $modelLabel = 'Local';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Estado')
                    ->schema([
                        Infolists\Components\TextEntry::make('status')
                            ->label('Estado')
                            ->badge(),
                    ]),

                Infolists\Components\Section::make('Datos del local')
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('nombre_local')
                            ->label('Nombre del local'),
                        Infolists\Components\TextEntry::make('cuit')
                            ->label('CUIT')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('direccion')
                            ->label('Dirección')
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('localidad')
                            ->label('Localidad (CABA)')
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('instagram')
                            ->label('Instagram')
                            ->placeholder('—'),
                    ]),

                Infolists\Components\Section::make('Contacto')
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('email')
                            ->label('Correo electrónico')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('telefono')
                            ->label('Teléfono'),
                    ]),

                Infolists\Components\Section::make('Descripción')
                    ->schema([
                        Infolists\Components\TextEntry::make('descripcion')
                            ->label('Descripción')
                            ->placeholder('Sin descripción')
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Registro')
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Fecha de alta')
                            ->dateTime('d/m/Y H:i'),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Última actualización')
                            ->dateTime('d/m/Y H:i'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre_local')
                    ->label('Local')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('direccion')
                    ->label('Dirección')
                    ->searchable()
                    ->limit(35),

                Tables\Columns\TextColumn::make('localidad')
                    ->label('Localidad')
                    ->placeholder('—')
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Correo')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('telefono')
                    ->label('Teléfono'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Alta')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options(LocalStatus::class),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Ver'),

                Tables\Actions\Action::make('suspender')
                    ->label('Suspender')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Suspender local')
                    ->modalDescription('¿Estás seguro de que querés suspender este local? Dejará de ser visible en la plataforma.')
                    ->modalSubmitActionLabel('Sí, suspender')
                    ->visible(fn (Local $record) => $record->status === LocalStatus::ACTIVE)
                    ->action(function (Local $record): void {
                        $record->update(['status' => LocalStatus::SUSPENDED]);

                        Notification::make()
                            ->title('Local suspendido')
                            ->warning()
                            ->send();
                    }),

                Tables\Actions\Action::make('activar')
                    ->label('Reactivar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Reactivar local')
                    ->modalDescription('¿Querés reactivar este local en la plataforma?')
                    ->modalSubmitActionLabel('Sí, reactivar')
                    ->visible(fn (Local $record) => $record->status === LocalStatus::SUSPENDED)
                    ->action(function (Local $record): void {
                        $record->update(['status' => LocalStatus::ACTIVE]);

                        Notification::make()
                            ->title('Local reactivado')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLocals::route('/'),
            'view'  => Pages\ViewLocal::route('/{record}'),
        ];
    }
}
