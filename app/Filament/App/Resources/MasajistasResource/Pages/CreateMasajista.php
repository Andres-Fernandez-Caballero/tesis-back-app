<?php

namespace App\Filament\App\Resources\MasajistasResource\Pages;

use App\Enums\Role;
use App\Filament\App\Resources\MasajistasResource;
use App\Mail\WelcomeTherapistMail;
use App\Models\User;
use App\Models\Users\UserData;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreateMasajista extends CreateRecord
{
    protected static string $resource = MasajistasResource::class;

    private string $generatedEmail    = '';
    private string $generatedPassword = '';
    private string $generatedDni      = '';
    private string $generatedTelefono = '';


    /**
     * Validar unicidad de campos virtuales (email y DNI) antes de crear el therapist.
     * Los campos virtuales no pertenecen al modelo MassageTherapist, por lo que
     * ->unique() en el form no es confiable: se valida aquí de forma explícita.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $email = $data['email'] ?? '';
        $dni   = $data['dni']   ?? '';

        $errors = [];

        if ($email && User::where('email', $email)->exists()) {
            $errors['email'] = 'El correo electrónico ya está registrado en el sistema.';
        }

        if ($dni && UserData::where('dni', $dni)->exists()) {
            $errors['dni'] = 'Ya existe un usuario registrado con ese DNI.';
        }

        if (! empty($errors)) {
            throw ValidationException::withMessages($errors);
        }

        // Guardar para usar en afterCreate()
        $this->generatedEmail    = $email;
        $this->generatedDni      = $dni;
        $this->generatedTelefono = $data['telefono'] ?? '';

        unset($data['email'], $data['dni'], $data['telefono']);

        $data['local_id'] = auth()->user()?->local?->id;
        $data['type']     = 'MassageTherapist';

        return $data;
    }

    /**
     * Una vez creado el masajista: crear User + UserData en una transacción,
     * asignar rol, vincular al therapist y enviar credenciales por email.
     */
    protected function afterCreate(): void
    {
        if (empty($this->generatedEmail)) {
            return;
        }

        $this->generatedPassword = Str::password(12, symbols: false);

        DB::transaction(function () {
            // Crear usuario
            $user = User::create([
                'name'                 => $this->record->nombre,
                'last_name'            => $this->record->apellido ?? '',
                'email'                => $this->generatedEmail,
                'password'             => Hash::make($this->generatedPassword),
                'must_change_password' => true,
            ]);

            $user->assignRole(Role::MASSAGE_THERAPIST->value);

            // Crear datos adicionales del usuario
            UserData::create([
                'user_id' => $user->id,
                'dni'     => $this->generatedDni,
                'phone'   => $this->generatedTelefono,
            ]);

            // Vincular therapist → user y guardar email
            $this->record->update([
                'user_id' => $user->id,
                'email'   => $this->generatedEmail,
            ]);

            // Enviar credenciales por email
            try {
                Mail::to($this->generatedEmail)
                    ->send(new WelcomeTherapistMail($user, $this->generatedPassword));
            } catch (\Throwable $e) {
                Log::error('No se pudo enviar el email de bienvenida al masajista', [
                    'masajista_id' => $this->record->id,
                    'email'        => $this->generatedEmail,
                    'error'        => $e->getMessage(),
                ]);
            }
        });

        // Notificación visual de confirmación
        Notification::make()
            ->title('Masajista creado — credenciales enviadas por email')
            ->body("Email: {$this->generatedEmail} · Contraseña temporal: {$this->generatedPassword}")
            ->success()
            ->persistent()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
