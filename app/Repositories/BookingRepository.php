<?php

namespace App\Repositories;

use App\Models\Therapists\Booking;
use Illuminate\Database\Eloquent\Model;

class BookingRepository
{
    public function create($data): Model
    {
        return Booking::create($data);
    }

    public function getAll(int $pagination = 10)
    {
        $query = Booking::with(['therapist.user', 'annuncement'])
            ->orderByDesc('created_at');
        if ($pagination) {
            return $query->paginate($pagination);
        }

        return $query->get();
    }

    public function getByClientId(int $clientId, int $pagination = 10)
    {
        $query = Booking::with(['therapist.user', 'annuncement', 'local', 'especialidad', 'review'])
            ->where('user_id', $clientId)
            ->orderByDesc('created_at');
        if ($pagination) {
            return $query->paginate($pagination);
        }

        return $query->get();
    }
}
