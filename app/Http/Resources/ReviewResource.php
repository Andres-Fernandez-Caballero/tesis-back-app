<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'bookingId'      => $this->booking_id,
            'localScore'     => $this->local_score,
            'therapistScore' => $this->therapist_score,
            'comment'        => $this->comment,
            'createdAt'      => $this->created_at,
        ];
    }
}
