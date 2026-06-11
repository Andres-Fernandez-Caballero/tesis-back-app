<?php

namespace App\Filament\App\Pages;

use App\Enums\Role;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;

class LocalConfiguracion extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-building-storefront';
    protected static ?string $navigationLabel = 'Mi Local';
    protected static ?string $title           = 'Configuración del Local';
    protected static ?int    $navigationSort  = 2;
    protected static string  $view            = 'filament.app.pages.local-configuracion';

    public ?array $data         = [];
    public ?array $passwordData = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole(Role::SPA_OWNER) ?? false;
    }

    protected function getForms(): array
    {
        return ['form', 'passwordForm'];
    }

    public function mount(): void
    {
        $local = auth()->user()?->local;

        $this->form->fill($local ? $local->only([
            'nombre_local',
            'direccion',
            'telefono',
            'email',
            'descripcion',
            'instagram',
            'localidad',
            'latitude',
            'longitude',
            'image',
            'slot_duration_minutes',
        ]) : []);

        $this->passwordForm->fill([]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Imagen del local')
                    ->description('Esta imagen se mostrará en la tarjeta del local dentro de la app.')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Imagen del local')
                            ->image()
                            ->disk('public')
                            ->directory('locals/images')
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->imagePreviewHeight('200')
                            ->helperText('Formatos permitidos: JPG, PNG, WEBP. Tamaño máximo: 2 MB.')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Datos del local')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('nombre_local')
                            ->label('Nombre del local')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('telefono')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(50),

                        Forms\Components\TextInput::make('direccion')
                            ->label('Dirección')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('email')
                            ->label('Correo electrónico de contacto')
                            ->email()
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('instagram')
                            ->label('Instagram')
                            ->prefix('@')
                            ->maxLength(100),

                        Forms\Components\Select::make('localidad')
                            ->label('Localidad (CABA)')
                            ->searchable()
                            ->options($this->localidades()),

                        Forms\Components\Textarea::make('descripcion')
                            ->label('Descripción del local')
                            ->placeholder('Contá sobre tu local, servicios y propuesta...')
                            ->rows(5)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Ubicación en el mapa')
                    ->description('Ingresá las coordenadas para que los clientes puedan encontrarte más fácilmente.')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Placeholder::make('coords_hint')
                            ->label('')
                            ->content('Podés obtener las coordenadas desde Google Maps haciendo clic derecho sobre la ubicación de tu local.')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('latitude')
                            ->label('Latitud')
                            ->numeric()
                            ->placeholder('-34.6037')
                            ->rules(['nullable', 'numeric', 'between:-90,90']),

                        Forms\Components\TextInput::make('longitude')
                            ->label('Longitud')
                            ->numeric()
                            ->placeholder('-58.3816')
                            ->rules(['nullable', 'numeric', 'between:-180,180']),
                    ]),

                Forms\Components\Section::make('Configuración de turnos')
                    ->description('Esta duración aplica a todos los masajistas del local.')
                    ->schema([
                        Forms\Components\Select::make('slot_duration_minutes')
                            ->label('Duración de cada turno')
                            ->options([
                                30  => '30 minutos',
                                45  => '45 minutos',
                                60  => '60 minutos (1 hora)',
                                90  => '90 minutos (1h 30min)',
                                120 => '120 minutos (2 horas)',
                            ])
                            ->default(60)
                            ->required(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $local = auth()->user()?->local;

        if (! $local) {
            Notification::make()->danger()->title('No se encontró el local asociado')->send();
            return;
        }

        $local->update($this->form->getState());

        Notification::make()->success()->title('Cambios guardados correctamente')->send();
    }

    public function passwordForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Cambiar contraseña')
                    ->description('Necesitás ingresar tu contraseña actual para poder establecer una nueva.')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('current_password')
                            ->label('Contraseña actual')
                            ->password()
                            ->revealable()
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('password')
                            ->label('Nueva contraseña')
                            ->password()
                            ->revealable()
                            ->minLength(6)
                            ->required(),

                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('Confirmar nueva contraseña')
                            ->password()
                            ->revealable()
                            ->same('password')
                            ->required(),
                    ]),
            ])
            ->statePath('passwordData');
    }

    public function savePassword(): void
    {
        $data = $this->passwordForm->getState();
        $user = auth()->user();

        if (! Hash::check($data['current_password'], $user->password)) {
            Notification::make()
                ->danger()
                ->title('La contraseña actual es incorrecta')
                ->send();
            return;
        }

        $user->update(['password' => Hash::make($data['password'])]);

        $this->passwordForm->fill([]);

        Notification::make()->success()->title('Contraseña actualizada correctamente')->send();
    }

    private function localidades(): array
    {
        return array_combine(
            $items = [
                'Agronomía','Almagro','Balvanera','Barracas','Belgrano','Boedo',
                'Caballito','Chacarita','Coghlan','Colegiales','Constitución',
                'Flores','Floresta','La Boca','La Paternal','Liniers','Mataderos',
                'Monte Castro','Montserrat','Nueva Pompeya','Núñez','Palermo',
                'Parque Avellaneda','Parque Chacabuco','Parque Chas','Parque Patricios',
                'Puerto Madero','Recoleta','Retiro','Saavedra','San Cristóbal',
                'San Nicolás','San Telmo','Versalles','Villa Crespo','Villa del Parque',
                'Villa Devoto','Villa General Mitre','Villa Lugano','Villa Luro',
                'Villa Ortúzar','Villa Pueyrredón','Villa Real','Villa Riachuelo',
                'Villa Santa Rita','Villa Soldati','Villa Urquiza','Villa Vélez Sarsfield',
            ],
            $items
        );
    }
}
