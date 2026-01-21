<?php

namespace App\Repositories;

use App\Models\Therapists\Availability;
use App\Models\Therapists\AvailabilityException;
use App\Models\Therapists\Booking;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class AvailabilityRepository
{
    /**
     * Get weekly availability ranges for a therapist on a given ISO day of week.
     */
    public function getWeeklyAvailability(
        int $therapistId,
        int $dayOfWeekIso
    ): Collection {
        return Availability::query()
            ->where('therapist_id', $therapistId)
            ->where('day_of_week', $dayOfWeekIso)
            ->orderBy('start_time')
            ->get();
    }

    /**
     * Get availability exceptions for a therapist on a specific date.
     */
    public function getExceptions(
        int $therapistId,
        Carbon $date
    ): Collection {
        return AvailabilityException::query()
            ->where('therapist_id', $therapistId)
            ->whereDate('date', $date->toDateString())
            ->get();
    }

    /**
     * Get active bookings for a therapist on a specific date.
     */
    public function getBookings(
        int $therapistId,
        Carbon $date
    ): Collection {
        return Booking::query()
            ->where('therapist_id', $therapistId)
            ->whereDate('date', $date->toDateString())
            ->whereIn('status', ['pending', 'confirmed'])
            ->get();
    }

    /**
     * Check if an availability range overlaps with an existing one.
     */
    public function availabilityOverlaps(
        int $therapistId,
        int $dayOfWeekIso,
        string $startTime,
        string $endTime,
        ?int $ignoreId = null
    ): bool {
        return Availability::query()
            ->where('therapist_id', $therapistId)
            ->where('day_of_week', $dayOfWeekIso)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where(function ($query) use ($startTime, $endTime) {
                $query
                    ->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $startTime);
            })
            ->exists();
    }
}
