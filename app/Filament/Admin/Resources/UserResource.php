<?php

namespace App\Filament\Admin\Resources;

use App\Enums\Role;
use App\Filament\Admin\Resources\UserResource\Pages;
use App\Models\User;
use App\Models\Users\States\ActiveUserState;
use App\Models\Users\States\BannedUserState;
use App\Models\Users\UserData;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Usuarios';

    protected static ?string $navigationGroup = 'Gestión';

    protected static ?string $pluralModelLabel = 'Usuarios';

    protected static ?string $modelLabel = 'Usuario';

    protected static ?int $navigationSort = 1;

    /**
     * Filtrar solo clientes (rol 'client').
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->role(Role::CLIENT->value);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Datos personales')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('last_name')
                            ->label('Apellido')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->label('Correo electrónico')
                            ->email()
                            ->required()
                            ->unique(User::class, 'email', ignoreRecord: true)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Contraseña')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->revealable()
                            ->required(fn (string $operation) => $operation === 'create')
                            ->dehydrated(fn (?string $state) => filled($state))
                            ->minLength(8),

                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('Confirmar contraseña')
                            ->password()
                            ->revealable()
                            ->same('password')
                            ->required(fn (string $operation) => $operation === 'create')
                            ->dehydrated(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('last_name')
                    ->label('Apellido')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('email')
                    ->label('Correo electrónico')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('state')
                    ->label('Estado')
                    ->badge()
                    ->color(fn ($record) => $record->state->color())
                    ->formatStateUsing(fn ($record) => $record->state->label())
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('state')
                    ->label('Estado')
                    ->options([
                        'ActiveUserState'    => 'Activo',
                        'BannedUserState'    => 'Bloqueado',
                        'SuspendedUserState' => 'Suspendido',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Editar'),

                Tables\Actions\Action::make('bloquear')
                    ->label('Bloquear')
                    ->icon('heroicon-o-lock-closed')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Bloquear usuario')
                    ->modalDescription('¿Estás seguro de que querés bloquear este usuario? No podrá acceder a la plataforma.')
                    ->modalSubmitActionLabel('Sí, bloquear')
                    ->visible(fn (User $record) => $record->state instanceof ActiveUserState)
                    ->action(function (User $record): void {
                        $record->state->transitionTo(BannedUserState::class);

                        Notification::make()
                            ->title('Usuario bloqueado')
                            ->warning()
                            ->send();
                    }),

                Tables\Actions\Action::make('desbloquear')
                    ->label('Desbloquear')
                    ->icon('heroicon-o-lock-open')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Desbloquear usuario')
                    ->modalDescription('¿Querés restaurar el acceso de este usuario?')
                    ->modalSubmitActionLabel('Sí, desbloquear')
                    ->visible(fn (User $record) => $record->state instanceof BannedUserState)
                    ->action(function (User $record): void {
                        $record->state->transitionTo(ActiveUserState::class);

                        Notification::make()
                            ->title('Usuario desbloqueado')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\DeleteAction::make()
                    ->label('Eliminar'),
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
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
