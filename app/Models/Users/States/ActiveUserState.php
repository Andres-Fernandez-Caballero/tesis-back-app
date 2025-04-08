<?php

namespace App\Models\Users\States;

class ActiveUserState extends AbstractUserState
{
    public function color(): string
    {
        return 'success';
    }

    public function label(): string
    {
        return 'Active';
    }

    public function description(): string
    {
        return 'The user is active and can access the system.';
    }

    public function isActive(): bool
    {
        return true;
    }
}