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
            'email' => $this->email,
            'role' => $this->roles->pluck('name')->first(),
            'address' => $this->user_data->address,
            'genre' => $this->user_data->genre,
            'birth_date' => $this->user_data->birth_date,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}