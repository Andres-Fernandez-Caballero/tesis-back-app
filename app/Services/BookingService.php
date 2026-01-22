<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\BookingRepository;

class BookingService
{
    public function __construct(
        private readonly BookingRepository $repository
    ) {}

    public function createBooking(array $data, User $client)
    {
        $data['user_id'] = $client->id;
        return $this->repository->create($data);
    }

    public function getAllBookings(int $pagination = 10)
    {
        return $this->repository->getAll($pagination);
    }

    public function getClientBookings(User $client, int $pagination = 10)
    {
        return $this->repository->getByClientId($client->id, $pagination);
    }
}