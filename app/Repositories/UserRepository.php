<?php

namespace App\Repositories;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserRepository
{
    public function getAll(int $pagination = 0)
    {
        if ($pagination) {
            return User::paginate($pagination);
        }

        return User::all();
    }

    public function getAllTherapists(int $pagination = 0)
    {
        if ($pagination) {
            return User::role(Role::MASSAGE_THERAPIST)->paginate($pagination);
        }
        return User::role(Role::MASSAGE_THERAPIST)->get();
    }

    public function getAllClients(int $pagination = 0)
    {
        if ($pagination) {
            return User::role(Role::CLIENT)->paginate($pagination);
        }

        return User::role(Role::CLIENT)->get();
    }
    public function findByEmail($email)
    {
        return User::where("email", $email)->first();
    }

    public function getClients()
    {
        return User::role(Role::CLIENT)->get();
    }

    public function getMassageTherapists()
    {
        return User::role(Role::MASSAGE_THERAPIST)->get();
    }

    public function findById(int $id)
    {
        return User::findOrFail($id);
    }

    public function create(array $data)
    {
        try {
            DB::beginTransaction();
            $user = User::create($data);
            $user->user_data()->create($data);

            DB::commit();
            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(int $id, array $data)
    {
        $user = User::findOrFail($id);
        $user->update($data);
        return $user;
    }

    public function delete(int $id)
    {
        $user = User::findOrFail($id);
        $user->delete();
    }
}
