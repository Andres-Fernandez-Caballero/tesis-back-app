<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class UserLoguedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'token' => $this->token,
            'user' => [
                'id' => $this->id,
                'name' => $this->name,
                'lastName' => $this->last_name,
                'profilePicture' => $this->user_data->profile_picture,
                'email' => $this->email,
                'role' => $this->roles->pluck('name')->first(),
            ]
        ];
    }
}
