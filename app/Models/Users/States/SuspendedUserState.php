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
        return __('user.suspended.label');
    }

    public function description(): string
    {
        return __('user.suspended.description');
    }

    public function isActive(): bool
    {
        return false;
    }
}