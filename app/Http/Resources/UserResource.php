<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Request are User Model
        return [
            'id' => $this->id,
            'name' => $this->name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'userData' => [
                'dni' => $this->user_data->dni,
                'role' => $this->roles->pluck('name')->first(),
                'address' => $this->user_data->address,
                'gender' => $this->user_data->gender,
                'birth_date' => $this->user_data->birth_date,     
            ],
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}