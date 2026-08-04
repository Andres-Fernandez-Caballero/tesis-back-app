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
        return __('user.active.label');
    }

    public function description(): string
    {
        return __('user.active.description');
    }

    public function isActive(): bool
    {
        return true;
    }
}