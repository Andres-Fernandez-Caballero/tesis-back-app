<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserManagementService
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAllUsers(int $pagination=0)
    {
        return $this->userRepository->getAll($pagination);
    }

    public function getAllTherapists(int $pagination=0)
    {
        return $this->userRepository->getAllTherapists($pagination);
    }

    public function getAllClients(int $pagination=0)
    {
        return $this->userRepository->getAllClients($pagination);
    }

    public function getProfile(int $userId)
    {
        return $this->userRepository->findById($userId);
    }

    public function updateProfile(int $userId, array $data)
    {
        return $this->userRepository->update($userId, $data);
    }

    public function deleteProfile(int $userId)
    {
        return $this->userRepository->delete($userId);
    }

    public function unBanUsers()
    {
        //
    }
}