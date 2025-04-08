<?php

namespace App\Models\Users\States;


class SuspendedUserState extends AbstractUserState
{
    public function color(): string
    {
        return 'warning';
    }

    public function label(): string
    {
        return 'Suspended';
    }

    public function description(): string
    {
        return 'The user is suspended and cannot access the system.';
    }

    public function isActive(): bool
    {
        return false;
    }
}