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
        return $this->hasRole(Role::ADMIN);
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