<?php

namespace App\Services;

use App\Enums\TransactionStatus;
use App\Models\Payments\Transaction;
use App\Models\Therapists\Announcement;
use App\Models\User;
use App\Notifications\UserNotification;
use App\Repositories\BookingRepository;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function __construct(
        private readonly BookingRepository $repository
    ) {}

    public function createBooking(array $data, User $client)
    {
        DB::beginTransaction();
        try {

            $data['user_id'] = $client->id;

            $booking = $this->repository->create($data);

            $announcement = Announcement::findOrFail($booking->announcement_id);

            $booking->transaction()->create([
                'client_id' => $client->id,
                'therapist_id' => $booking->therapist->user->id,
                'amount' => $announcement->price,
                'currency' => $announcement->currency,
                'status' => TransactionStatus::PENDING,
                'description' => "Reserva de turno para el día {$booking->appointment_date} a las {$booking->appointment_time}",
            ]);

            // NOTIFICAR AL CLIENTE
            $booking->user->notify(
                new UserNotification(
                    title: "Nueva solicitud de turno enviada",
                    body: "Turno solicitad, espera la confirmacion del masajista",
                    url: '/confirmation/',
                    view: 'emails.booking-created-client',
                    viewData: []
                )
            );

            

            // NOTIFICAR AL MASAJISTA
            $booking->therapist->user->notify(
                new UserNotification(
                    title: "Nueva solicitud de turno",
                    body: "Un cliente solicito un turno",
                    url: '/confirmation/',
                    view: 'emails.booking-created-client',
                    viewData: [
                        'therapist' => $booking->therapist,
                        'client' => $booking->user,
                        'appointment' => 'appointment',
                        'confirmUrl' => 'confirmur'
                    ]
                )
            );
            DB::commit();
            return $booking;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
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
