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

class MiPerfilMasajista extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-user-circle';
    protected static ?string $navigationLabel = 'Mi Perfil';
    protected static ?string $title           = 'Mi Perfil';
    protected static ?int    $navigationSort  = 1;
    protected static string  $view            = 'filament.app.pages.mi-perfil-masajista';

    public ?array $fotoData     = [];
    public ?array $passwordData = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole(Role::MASSAGE_THERAPIST) ?? false;
    }

    protected function getForms(): array
    {
        return ['fotoForm', 'passwordForm'];
    }

    public function fotoForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Mi foto de perfil')
                    ->description('Esta imagen será visible para los clientes al momento de seleccionar un masajista.')
                    ->schema([
                        Forms\Components\FileUpload::make('foto_url')
                            ->label('Foto')
                            ->image()
                            ->disk('public')
                            ->directory('therapists/photos')
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->imagePreviewHeight('200')
                            ->helperText('Formatos permitidos: JPG, PNG, WEBP. Tamaño máximo: 2 MB.')
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('fotoData');
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

    public function mount(): void
    {
        $therapist = auth()->user()?->therapist;

        $this->fotoForm->fill([
            'foto_url' => $therapist?->foto_url,
        ]);

        $this->passwordForm->fill([]);
    }

    public function saveFoto(): void
    {
        $data      = $this->fotoForm->getState();
        $therapist = auth()->user()?->therapist;

        if (! $therapist) {
            Notification::make()->danger()->title('No se encontró el perfil de masajista')->send();
            return;
        }

        $therapist->update(['foto_url' => $data['foto_url']]);

        Notification::make()->success()->title('Foto actualizada correctamente')->send();
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
}
