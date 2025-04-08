<?php

namespace App\Core\UseCases\UserManagement;

use App\Enums\Permission;
use App\Enums\Role;
use App\Models\Users\States\BannedUserState;
use App\Repositories\UserRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Exception;

class BanUser
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
    }

    public function execute(string $userId, string $bannedTo): void
    {
        // Verificar permisos del usuario actual
        if (!Auth::user()->can(Permission::ADMINISTRATE_BANNED_USERS->value)) {
            throw new AuthorizationException('No tienes permiso para banear usuarios');
        }

        // Buscar usuario por ID
        $user = $this->userRepository->findById($userId);
        if ($user === null) {
            throw new Exception('Usuario no encontrado');
        }

        // No permitir banear a administradores
        if ($user->roles->pluck('name')->contains(Role::ADMIN->value)) {
            throw new AuthorizationException('No se puede banear a un administrador');
        }

        // Validar y parsear la fecha
        try {
            $bannedUntil = Carbon::parse($bannedTo);
        } catch (\Throwable $e) {
            throw new Exception('Fecha inválida para el baneo');
        }

        // Transicionar estado y guardar
        $user->state->transitionTo(BannedUserState::class);
        $user->banned_to = $bannedUntil;
        $user->save();
    }
}
