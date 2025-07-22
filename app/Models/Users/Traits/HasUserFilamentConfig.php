<?php

namespace App\Models\Users\Traits;

use App\Enums\Role;
use Filament\Panel;

/* 
    * Este trait agrupa todas las configuraciones de paneles de Filament
    * debe implenentarse en el modelo User las siguientes interfaces
        - FilamentUser
        - HasAvatar
        - HasName
    * y el trait HasUserFilamentConfig
*/

trait HasUserFilamentConfig
{
    public function canAccessPanel(Panel $panel): bool
    {
        $panelId = $panel->getId();
        switch ($panelId) {
            case 'admin':
                return $this->canAccessAdminPanel();
                // Agregar más casos para otros paneles si es necesario
            case 'app':
                return $this->canAccessAppPanel();
            default:
                return false;
        }
        return $this->hasRole(Role::ADMIN);
    }

    public function canAccessAdminPanel(): bool
    {
        return $this->hasRole(Role::ADMIN);
    }

    public function canAccessAppPanel(): bool
    {
        return $this->hasRole([Role::CLIENT, Role::MASSAGE_THERAPIST]);
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->user_data?->profile_picture;
    }

    public function getFilamentName(): string
    {
        return "{$this->name}";
    }
}
