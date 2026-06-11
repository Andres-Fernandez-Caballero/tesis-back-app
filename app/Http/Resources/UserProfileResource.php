<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'user' => [
                'id'             => $this->id,
                'name'           => $this->name,
                'lastName'       => $this->last_name,
                'email'          => $this->email,
                'role'           => $this->roles->pluck('name')->first(),
                'profilePicture' => $this->user_data?->profile_picture ?? null,
            ],
        ];
    }
}
