<?php

namespace App\Filament\Admin\Resources;

use App\Enums\LocalRegistrationStatus;
use App\Enums\Role;
use App\Filament\Admin\Resources\LocalRegistrationResource\Pages;
use App\Mail\LocalRegistrationApproved;
use App\Mail\LocalRegistrationRejected;
use App\Models\Local;
use App\Models\LocalRegistration;
use App\Models\User;
use App\Models\Users\UserData;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role as SpatieRole;

class LocalRegistrationResource extends Resource
{
    protected static ?string $model = LocalRegistration::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Solicitudes';

    protected static ?string $navigationGroup = 'Locales';

    protected static ?string $pluralModelLabel = 'Solicitudes de Alta';

    protected static ?string $modelLabel = 'Solicitud';

    protected static ?int $navigationSort = 1;

    /**
     * Mostrar badge con la cantidad de solicitudes pendientes en el menú.
     */
    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', LocalRegistrationStatus::PENDING)->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Estado de la solicitud')
                    ->schema([
                        Infolists\Components\TextEntry::make('status')
                            ->label('Estado')
                            ->badge(),
                    ]),

                Infolists\Components\Section::make('Datos del dueño')
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('nombre')
                            ->label('Nombre')
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('apellido')
                            ->label('Apellido')
                            ->placeholder('—'),
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
                        Infolists\Components\TextEntry::make('latitude')
                            ->label('Latitud')
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('longitude')
                            ->label('Longitud')
                            ->placeholder('—'),
                    ]),

                Infolists\Components\Section::make('Datos de contacto')
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

                Infolists\Components\Section::make('Metadatos')
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Fecha de registro')
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

                Tables\Columns\TextColumn::make('nombre')
                    ->label('Responsable')
                    ->formatStateUsing(fn ($record) => trim(($record->nombre ?? '') . ' ' . ($record->apellido ?? '')))
                    ->searchable(['nombre', 'apellido']),

                Tables\Columns\TextColumn::make('email')
                    ->label('Correo')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('telefono')
                    ->label('Teléfono'),

                Tables\Columns\TextColumn::make('localidad')
                    ->label('Localidad')
                    ->placeholder('—')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options(LocalRegistrationStatus::class)
                    ->default(LocalRegistrationStatus::PENDING->value),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Ver'),

                Tables\Actions\Action::make('aprobar')
                    ->label('Aprobar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Aprobar solicitud')
                    ->modalDescription('¿Estás seguro de que querés aprobar este local? Se creará el registro y se notificará al responsable por email.')
                    ->modalSubmitActionLabel('Sí, aprobar')
                    ->visible(fn (LocalRegistration $record) => $record->status === LocalRegistrationStatus::PENDING)
                    ->action(function (LocalRegistration $record): void {
                        // 1. Generar contraseña aleatoria
                        $password = Str::password(12, symbols: false);

                        // 2. Crear usuario dueño del local (o reutilizar si ya existe)
                        $owner = User::firstOrCreate(
                            ['email' => $record->email],
                            [
                                'name'                 => $record->nombre ?? $record->nombre_local,
                                'last_name'            => $record->apellido,
                                'password'             => Hash::make($password),
                                'must_change_password' => true,
                            ]
                        );

                        // Crear UserData si no existe
                        UserData::firstOrCreate(['user_id' => $owner->id]);

                        // 3. Asignar rol spa_owner (crear el rol si no existe aún)
                        SpatieRole::firstOrCreate(['name' => Role::SPA_OWNER->value, 'guard_name' => 'web']);
                        $owner->assignRole(Role::SPA_OWNER->value);

                        // 4. Crear el local y linkearlo al usuario
                        $local = Local::create([
                            'nombre_local'          => $record->nombre_local,
                            'direccion'             => $record->direccion,
                            'cuit'                  => $record->cuit,
                            'telefono'              => $record->telefono,
                            'email'                 => $record->email,
                            'descripcion'           => $record->descripcion,
                            'localidad'             => $record->localidad,
                            'latitude'              => $record->latitude,
                            'longitude'             => $record->longitude,
                            'instagram'             => $record->instagram,
                            'status'                => 'active',
                            'local_registration_id' => $record->id,
                            'user_id'               => $owner->id,
                        ]);

                        // 5. Geocodificar si no vienen coordenadas en la solicitud
                        if (! $local->latitude || ! $local->longitude) {
                            self::geocodeLocal($local, $record->direccion, $record->localidad);
                        }

                        // 6. Crear especialidades predefinidas para el local
                        $local->seedDefaultEspecialidades();

                        // 6. Actualizar estado de la solicitud
                        $record->update(['status' => LocalRegistrationStatus::APPROVED]);

                        // 7. Enviar email con credenciales
                        try {
                            Mail::to($record->email)->send(new LocalRegistrationApproved($record, $password));
                        } catch (\Exception) {
                            // El mail falla silenciosamente para no bloquear la acción
                        }

                        Notification::make()
                            ->title('Local aprobado y credenciales enviadas')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('rechazar')
                    ->label('Rechazar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Rechazar solicitud')
                    ->modalDescription('¿Estás seguro de que querés rechazar esta solicitud? Se notificará al responsable por email.')
                    ->modalSubmitActionLabel('Sí, rechazar')
                    ->visible(fn (LocalRegistration $record) => $record->status === LocalRegistrationStatus::PENDING)
                    ->action(function (LocalRegistration $record): void {
                        $record->update(['status' => LocalRegistrationStatus::REJECTED]);

                        try {
                            Mail::to($record->email)->send(new LocalRegistrationRejected($record));
                        } catch (\Exception) {
                            // silencioso
                        }

                        Notification::make()
                            ->title('Solicitud rechazada')
                            ->warning()
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
            'index' => Pages\ListLocalRegistrations::route('/'),
            'view'  => Pages\ViewLocalRegistration::route('/{record}'),
        ];
    }

    // ─── Geocodificación ────────────────────────────────────────────────────────

    /**
     * Intenta obtener lat/lng desde la dirección usando Google Geocoding API.
     * Falla silenciosamente — el local queda sin coordenadas si la API no responde.
     */
    private static function geocodeLocal(Local $local, ?string $direccion, ?string $localidad): void
    {
        $key = config('services.google.maps_api_key');

        if (! $key || ! $direccion) {
            return;
        }

        $address = implode(', ', array_filter([
            $direccion,
            $localidad,
            'Buenos Aires',
            'Argentina',
        ]));

        try {
            $response = Http::timeout(8)->get('https://maps.googleapis.com/maps/api/geocode/json', [
                'address' => $address,
                'key'     => $key,
            ]);

            $data = $response->json();

            if (($data['status'] ?? '') === 'OK' && ! empty($data['results'])) {
                $loc = $data['results'][0]['geometry']['location'];
                $local->update([
                    'latitude'  => $loc['lat'],
                    'longitude' => $loc['lng'],
                ]);
            }
        } catch (\Exception) {
            // Falla silenciosamente; el admin puede cargar las coords manualmente
        }
    }
}
