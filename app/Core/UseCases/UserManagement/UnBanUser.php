<?php

namespace App\Core\UseCases\UserManagement;

use App\Enums\Permission;
use App\Enums\Role;
use App\Models\Users\States\ActiveUserState;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Support\Facades\Auth;

class UnBanUser
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
    }

    public function execute(string $userId): void
    {
        // Verificar permisos del usuario actual
        if (!Auth::user()->can(Permission::ADMINISTRATE_BANNED_USERS->value)) {
            throw new Exception('No tienes permiso para desbanear usuarios');
        }

        // Buscar usuario por ID
        $user = $this->userRepository->findById($userId);
        if ($user === null) {
            throw new Exception('Usuario no encontrado');
        }

        // Transicionar estado y guardar
        $user->state->transitionTo(ActiveUserState::class);
        $user->banned_to = null;
        $user->save();
    }
}