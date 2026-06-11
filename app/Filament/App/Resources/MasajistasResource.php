<?php

namespace App\Filament\App\Resources;

use App\Enums\Role;
use App\Filament\App\Resources\MasajistasResource\Pages;
use App\Filament\App\Resources\MasajistasResource\RelationManagers\DisponibilidadRelationManager;
use App\Filament\App\Resources\MasajistasResource\RelationManagers\ExcepcionesRelationManager;
use App\Models\Especialidad;
use App\Models\Therapists\MassageTherapist;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class MasajistasResource extends Resource
{
    protected static ?string $model = MassageTherapist::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Masajistas';

    protected static ?string $pluralModelLabel = 'Masajistas';

    protected static ?string $modelLabel = 'Masajista';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole(Role::SPA_OWNER) ?? false;
    }

    /**
     * Sólo los masajistas del local del dueño autenticado.
     */
    public static function getEloquentQuery(): Builder
    {
        $localId = auth()->user()?->local?->id;

        return parent::getEloquentQuery()
            ->when($localId, fn (Builder $q) => $q->where('local_id', $localId))
            ->when(! $localId, fn (Builder $q) => $q->whereRaw('0 = 1'));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Datos del masajista')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('nombre')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('apellido')
                            ->label('Apellido')
                            ->required()
                            ->maxLength(255),

                        // Credenciales — visibles solo en create
                        // La unicidad de email y DNI se valida en CreateMasajista::mutateFormDataBeforeCreate()
                        Forms\Components\TextInput::make('email')
                            ->label('Correo electrónico')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->visibleOn('create')
                            ->helperText('Se enviará un email con las credenciales de acceso al portal.'),

                        Forms\Components\TextInput::make('dni')
                            ->label('DNI')
                            ->required()
                            ->maxLength(20)
                            ->visibleOn('create'),

                        Forms\Components\TextInput::make('telefono')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(30)
                            ->visibleOn('create'),

                        Forms\Components\Toggle::make('activo')
                            ->label('Activo')
                            ->default(true)
                            ->inline(false),

                        Forms\Components\CheckboxList::make('especialidades')
                            ->label('Especialidades del masajista')
                            ->relationship('especialidades', 'nombre')
                            ->options(function (): array {
                                $localId = auth()->user()?->local?->id;
                                if (! $localId) {
                                    return [];
                                }
                                return Especialidad::where('local_id', $localId)
                                    ->orderBy('nombre')
                                    ->pluck('nombre', 'id')
                                    ->toArray();
                            })
                            ->hint('Solo aparecen las especialidades habilitadas por tu local.')
                            ->columns(2)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('descripcion')
                            ->label('Descripción / Bio')
                            ->placeholder('Una breve presentación del masajista...')
                            ->rows(4)
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('foto_url')
                            ->label('Foto del masajista')
                            ->image()
                            ->disk('public')
                            ->directory('therapists/photos')
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->imagePreviewHeight('160')
                            ->helperText('Formatos permitidos: JPG, PNG, WEBP. Tamaño máximo: 2 MB.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('apellido')
                    ->label('Apellido')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('especialidades.nombre')
                    ->label('Especialidades')
                    ->badge()
                    ->separator(',')
                    ->placeholder('Sin especialidades'),

                Tables\Columns\IconColumn::make('activo')
                    ->label('Estado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Agregado')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('activo')
                    ->label('Estado')
                    ->trueLabel('Solo activos')
                    ->falseLabel('Solo inactivos'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Editar'),

                Tables\Actions\Action::make('cambiar_contrasena')
                    ->label('Cambiar contraseña')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->modalHeading(fn (MassageTherapist $record): string =>
                        'Cambiar contraseña — ' . trim($record->nombre . ' ' . ($record->apellido ?? ''))
                    )
                    ->modalDescription(fn (MassageTherapist $record): string =>
                        'Establecé una nueva contraseña para ' .
                        trim($record->nombre . ' ' . ($record->apellido ?? '')) .
                        ' (' . ($record->email ?? '—') . '). ' .
                        'El masajista deberá cambiarla en su próximo acceso al portal.'
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
                    ->action(function (MassageTherapist $record, array $data): void {
                        if (! $record->user) {
                            Notification::make()
                                ->danger()
                                ->title('El masajista no tiene un usuario asociado')
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
                            ->body(
                                'La contraseña de ' .
                                trim($record->nombre . ' ' . ($record->apellido ?? '')) .
                                ' fue actualizada. Deberá cambiarla en su próximo acceso.'
                            )
                            ->send();
                    }),

                Tables\Actions\DeleteAction::make()->label('Eliminar'),
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
        return [
            DisponibilidadRelationManager::class,
            ExcepcionesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMasajistas::route('/'),
            'create' => Pages\CreateMasajista::route('/create'),
            'edit'   => Pages\EditMasajista::route('/{record}/edit'),
        ];
    }
}
