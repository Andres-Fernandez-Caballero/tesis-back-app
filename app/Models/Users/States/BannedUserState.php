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
        return __('user.banned.label');
    }

    public function description(): string
    {
        return __('user.banned.description');
    }

    public function isActive(): bool
    {
        return false;
    }
}