<?php

namespace App\Services\User;

use App\Repositories\UserRepository;

class UserStateManagementService
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {}

  
    public function banUser()
    {

    }
}
