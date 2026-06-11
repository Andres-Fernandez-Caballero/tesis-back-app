<?php

namespace App\Filament\Admin\Resources;

use App\Enums\Role;
use App\Filament\Admin\Resources\LocalesAdminResource\Pages;
use App\Models\Local;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class LocalesAdminResource extends Resource
{
    protected static ?string $model = Local::class;

    protected static ?string $slug            = 'locales';
    protected static ?string $navigationIcon  = 'heroicon-o-building-storefront';
    protected static ?string $navigationLabel = 'Locales';
    protected static ?string $pluralModelLabel = 'Locales';
    protected static ?string $modelLabel       = 'Local';
    protected static ?string $navigationGroup  = 'Gestión de usuarios';
    protected static ?int    $navigationSort   = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole(Role::ADMIN) ?? false;
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

                Tables\Columns\TextColumn::make('responsable')
                    ->label('Responsable')
                    ->getStateUsing(fn (Local $record): string =>
                        trim(($record->user?->name ?? '') . ' ' . ($record->user?->last_name ?? '')) ?: '—'
                    )
                    ->searchable(query: fn ($query, string $search) =>
                        $query->whereHas('user', fn ($q) =>
                            $q->where('name', 'like', "%{$search}%")
                              ->orWhere('last_name', 'like', "%{$search}%")
                        )
                    ),

                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('localidad')
                    ->label('Localidad')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('user.must_change_password')
                    ->label('Debe cambiar contraseña')
                    ->boolean()
                    ->trueIcon('heroicon-o-exclamation-triangle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('warning')
                    ->falseColor('success'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrado')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\Action::make('cambiar_contrasena')
                    ->label('Cambiar contraseña')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->modalHeading(fn (Local $record): string =>
                        'Cambiar contraseña — ' . $record->nombre_local
                    )
                    ->modalDescription(fn (Local $record): string =>
                        'Establecé una nueva contraseña para ' .
                        trim(($record->user?->name ?? '') . ' ' . ($record->user?->last_name ?? '')) .
                        ' (' . ($record->user?->email ?? '—') . '). ' .
                        'El usuario deberá cambiarla en su próximo acceso al portal.'
                    )
                    ->modalSubmitActionLabel('Guardar contraseña')
                    ->form([
                        Forms\Components\TextInput::make('password')
                            ->label('Nueva contraseña')
                            ->password()
                            ->revealable()
                            ->required()
                            ->minLength(8)
                            ->helperText('Mínimo 8 caracteres.'),

                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('Confirmar nueva contraseña')
                            ->password()
                            ->revealable()
                            ->required()
                            ->same('password'),
                    ])
                    ->action(function (Local $record, array $data): void {
                        if (! $record->user) {
                            Notification::make()
                                ->danger()
                                ->title('El local no tiene un usuario asociado')
                                ->send();

                            return;
                        }

                        $record->user->update([
                            'password'             => Hash::make($data['password']),
                            'must_change_password' => true,
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Contraseña actualizada')
                            ->body("La contraseña de {$record->user->email} fue actualizada. El usuario deberá cambiarla en su próximo acceso.")
                            ->send();
                    }),
            ])
            ->bulkActions([])
            ->defaultSort('nombre_local', 'asc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLocalesAdmin::route('/'),
        ];
    }
}
