<?php

namespace App\Services;

use App\Models\Users\UserData;
use App\Repositories\UserRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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
        // Quitar current_password antes de persistir (sólo se usa para validación)
        unset($data['current_password']);
        return $this->userRepository->update($userId, $data);
    }

    public function uploadProfilePhoto(int $userId, UploadedFile $file)
    {
        $path = $file->storeAs(
            'profile_pictures',
            $userId . '.' . $file->getClientOriginalExtension(),
            'public'
        );

        $url = Storage::disk('public')->url($path);

        UserData::updateOrCreate(
            ['user_id' => $userId],
            ['profile_picture' => $url]
        );

        return $this->userRepository->findById($userId);
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