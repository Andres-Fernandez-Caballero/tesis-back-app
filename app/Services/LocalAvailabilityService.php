<?php

namespace App\Services;

use App\Models\Local;
use App\Models\Therapists\Booking;
use App\Models\Therapists\States\Booking\BookingConfirmed;
use App\Models\Therapists\States\Booking\BookingPending;
use App\Models\Therapists\States\Booking\BookingPendingPayment;
use App\Repositories\AvailabilityRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class LocalAvailabilityService
{
    public function __construct(
        private readonly AvailabilityRepository $availabilityRepository,
        private readonly AvailabilityService $availabilityService,
    ) {}

    /**
     * Returns days with merged available slots across all active masajistas.
     * A slot appears if at least one masajista is free at that time.
     */
    public function getAvailableSlots(Local $local, int $daysAhead = 14): array
    {
        $masajistas = $local->therapists()->where('activo', true)->get();

        if ($masajistas->isEmpty()) {
            return [];
        }

        $merged = [];

        foreach ($masajistas as $masajista) {
            $days = $this->availabilityService->getAvailableDays(
                therapistId: $masajista->id,
                serviceDurationMinutes: $local->slot_duration_minutes,
                daysAhead: $daysAhead
            );

            foreach ($days as $day) {
                if (empty($day->slots)) {
                    continue;
                }

                if (! isset($merged[$day->date])) {
                    $merged[$day->date] = [
                        'date'       => $day->date,
                        'dayOfWeek'  => $day->dayOfWeek,
                        'slots'      => [],
                    ];
                }

                foreach ($day->slots as $slot) {
                    $merged[$day->date]['slots'][$slot->start] = [
                        'startTime' => $slot->start,
                        'endTime'   => $slot->end,
                    ];
                }
            }
        }

        ksort($merged);

        return array_values(array_map(function (array $day): array {
            ksort($day['slots']);
            return [
                'date'      => $day['date'],
                'dayOfWeek' => $day['dayOfWeek'],
                'slots'     => array_values($day['slots']),
            ];
        }, $merged));
    }

    /**
     * Returns masajistas who are free on a specific date + time slot.
     * Optionally filtered by especialidad.
     */
    public function getAvailableMasajistas(
        Local $local,
        string $date,
        string $startTime,
        ?int $especialidadId = null
    ): Collection {
        $carbonDate = Carbon::parse($date);
        $endTime = Carbon::createFromFormat('H:i', $startTime)
            ->addMinutes($local->slot_duration_minutes)
            ->format('H:i');

        $query = $local->therapists()
            ->with('especialidades')
            ->where('activo', true);

        if ($especialidadId) {
            $query->whereHas(
                'especialidades',
                fn ($q) => $q->where('especialidades.id', $especialidadId)
            );
        }

        return $query->get()->filter(function ($masajista) use ($carbonDate, $date, $startTime, $endTime): bool {
            // Weekly availability must cover the full slot window
            $hasWindow = $this->availabilityRepository
                ->getWeeklyAvailability($masajista->id, $carbonDate->dayOfWeekIso)
                ->contains(fn ($av) =>
                    substr($av->start_time, 0, 5) <= $startTime &&
                    substr($av->end_time, 0, 5) >= $endTime
                );

            if (! $hasWindow) {
                return false;
            }

            // Reject if a full-day exception exists
            $exceptions = $this->availabilityRepository->getExceptions($masajista->id, $carbonDate);
            if ($exceptions->contains(fn ($e) => is_null($e->start_time) && is_null($e->end_time))) {
                return false;
            }

            // Reject if an active booking overlaps the slot
            return ! Booking::where('therapist_id', $masajista->id)
                ->whereDate('date', $date)
                ->where('start_time', '<', $endTime)
                ->where('end_time', '>', $startTime)
                ->whereState('state', [BookingPendingPayment::class, BookingPending::class, BookingConfirmed::class])
                ->exists();
        })->values();
    }
}
