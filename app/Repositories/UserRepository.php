<?php
namespace App\Repositories;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserRepository
{
    public function getAll()
    {
        return User::paginate(10);
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
        return User::create($data);
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
