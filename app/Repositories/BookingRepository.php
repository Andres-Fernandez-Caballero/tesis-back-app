<?php

namespace App\Repositories;

use App\Models\Therapists\Booking;

class BookingRepository
{
    public function create($data) {
        Booking::create($data);
    }

    public function getAll(int $pagination = 10) {
        $query = Booking::orderByDesc('created_at');
        if ($pagination) {
            return $query->paginate($pagination);
        }

        return $query->get();
    }

    public function getByClientId(int $clientId, int $pagination = 10) {
        $query = Booking::where('user_id', $clientId)
            ->orderByDesc('created_at');
        if ($pagination) {
            return $query->paginate($pagination);
        }

        return $query->get();
    }
}