<?php

namespace App\Models\Users\States;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class AbstractUserState extends State
{
    abstract public function color(): string;
    abstract public function label(): string;
    abstract public function description(): string;
    abstract public function isActive(): bool;
    

    public static function config(): StateConfig
    {
        return parent::config()
            ->default(ActiveUserState::class)

            /* 
             * Define las transacciones posibles entre los estados
            */
            ->allowTransition(ActiveUserState::class, BannedUserState::class)
            ->allowTransition(ActiveUserState::class, SuspendedUserState::class)
            ->allowTransition(BannedUserState::class, ActiveUserState::class)
            ->allowTransition(BannedUserState::class, SuspendedUserState::class)
            ->allowTransition(SuspendedUserState::class, ActiveUserState::class)
        ;
    }
}