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

class CambiarContrasena extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = null;
    protected static bool    $shouldRegisterNavigation = false;
    protected static ?string $title = 'Cambiar contraseña';
    protected static string  $view  = 'filament.app.pages.cambiar-contrasena';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user?->hasRole(Role::SPA_OWNER) || $user?->hasRole(Role::MASSAGE_THERAPIST);
    }

    public function mount(): void
    {
        // Si el usuario no tiene el flag activo, no hay razón para estar aquí.
        if (! auth()->user()?->must_change_password) {
            $this->redirect(filament()->getCurrentPanel()->getUrl());
            return;
        }

        $this->form->fill([]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Establecer nueva contraseña')
                    ->description('Por seguridad, debés establecer una contraseña propia antes de continuar. No podrás acceder al portal hasta completar este paso.')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('current_password')
                            ->label('Contraseña actual (temporal)')
                            ->password()
                            ->revealable()
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('password')
                            ->label('Nueva contraseña')
                            ->password()
                            ->revealable()
                            ->required()
                            ->minLength(8)
                            ->different('current_password')
                            ->helperText('Mínimo 8 caracteres. Debe ser diferente a la contraseña actual.'),

                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('Confirmar nueva contraseña')
                            ->password()
                            ->revealable()
                            ->required()
                            ->same('password'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $user = auth()->user();

        if (! Hash::check($data['current_password'], $user->password)) {
            Notification::make()
                ->danger()
                ->title('La contraseña actual es incorrecta')
                ->send();

            return;
        }

        $user->update([
            'password'             => Hash::make($data['password']),
            'must_change_password' => false,
        ]);

        Notification::make()
            ->success()
            ->title('Contraseña actualizada correctamente')
            ->body('Ya podés acceder al portal con tu nueva contraseña.')
            ->send();

        $this->redirect(filament()->getCurrentPanel()->getUrl());
    }
}
