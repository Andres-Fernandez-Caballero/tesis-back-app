<?php

namespace App\Models\Users\States;

class BannedUserState extends AbstractUserState
{
    public function color(): string
    {
        return 'danger';
    }

    public function label(): string
    {
        return 'Banned';
    }

    public function description(): string
    {
        return 'The user is banned and cannot access the system.';
    }

    public function isActive(): bool
    {
        return false;
    }
}