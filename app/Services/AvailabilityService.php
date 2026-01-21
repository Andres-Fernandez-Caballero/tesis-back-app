<?php

namespace App\Services;

use App\DTOs\DayAvailabilityDTO;
use App\DTOs\SlotDTO;
use App\Repositories\AvailabilityRepository;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

class AvailabilityService
{
    public function __construct(
        private readonly AvailabilityRepository $repository
    ) {}

    /**
     * @return DayAvailabilityDTO[]
     */
    public function getAvailableDays(
        int $therapistId,
        int $serviceDurationMinutes,
        int $daysAhead = 14
    ): array {
        $days = [];

        $period = CarbonPeriod::create(
            Carbon::today(),
            Carbon::today()->addDays($daysAhead)
        );

        foreach ($period as $date) {
            $days[] = $this->buildDayAvailability(
                $therapistId,
                $date,
                $serviceDurationMinutes
            );
        }

        return $days;
    }

    private function buildDayAvailability(
        int $therapistId,
        Carbon $date,
        int $durationMinutes
    ): DayAvailabilityDTO {
        $exceptions = $this->repository->getExceptions($therapistId, $date);

        /** 🚫 Full-day exception → NO slots */
        if ($this->hasFullDayException($exceptions)) {
            return new DayAvailabilityDTO(
                date: $date->toDateString(),
                dayOfWeek: $date->dayOfWeekIso,
                available: false,
                reason: 'available_exception_full_day',
                slots: []
            );
        }

        $weeklyAvailability = $this->repository
            ->getWeeklyAvailability($therapistId, $date->dayOfWeekIso);

        /** 🚫 Not a working day */
        if ($weeklyAvailability->isEmpty()) {
            return new DayAvailabilityDTO(
                date: $date->toDateString(),
                dayOfWeek: $date->dayOfWeekIso,
                available: false,
                reason: 'not_working_day',
                slots: []
            );
        }

        $bookings = $this->repository->getBookings($therapistId, $date);
        $partialExceptions = $this->getPartialExceptions($exceptions);

        $slots = [];

        foreach ($weeklyAvailability as $availability) {
            $slots = array_merge(
                $slots,
                $this->generateSlots(
                    $availability->start_time,
                    $availability->end_time,
                    $durationMinutes,
                    $bookings,
                    $partialExceptions
                )
            );
        }

        return new DayAvailabilityDTO(
            date: $date->toDateString(),
            dayOfWeek: $date->dayOfWeekIso,
            available: true, // 👈 working day aunque no haya slots
            reason: empty($slots) ? 'no_slots' : null,
            slots: $slots
        );
    }

    /**
     * @return SlotDTO[]
     */
    private function generateSlots(
        string $startTime,
        string $endTime,
        int $durationMinutes,
        Collection $bookings,
        Collection $exceptions
    ): array {
        $slots = [];

        $cursor = Carbon::createFromFormat('H:i', substr($startTime, 0, 5));
        $end = Carbon::createFromFormat('H:i', substr($endTime, 0, 5));

        while ($cursor->copy()->addMinutes($durationMinutes)->lte($end)) {
            $slotStart = $cursor->copy();
            $slotEnd = $cursor->copy()->addMinutes($durationMinutes);

            if (
                $this->overlaps($slotStart, $slotEnd, $bookings) ||
                $this->overlaps($slotStart, $slotEnd, $exceptions)
            ) {
                $cursor->addMinutes($durationMinutes);
                continue;
            }

            $slots[] = new SlotDTO(
                start: $slotStart->format('H:i'),
                end: $slotEnd->format('H:i')
            );

            $cursor->addMinutes($durationMinutes);
        }

        return $slots;
    }

    private function overlaps(
        Carbon $start,
        Carbon $end,
        Collection $ranges
    ): bool {
        foreach ($ranges as $range) {
            if (!$range->start_time || !$range->end_time) {
                continue;
            }

            $rangeStart = Carbon::createFromFormat('H:i', substr($range->start_time, 0, 5));
            $rangeEnd = Carbon::createFromFormat('H:i', substr($range->end_time, 0, 5));

            if ($rangeStart < $end && $rangeEnd > $start) {
                return true;
            }
        }

        return false;
    }

    private function hasFullDayException(Collection $exceptions): bool
    {
        return $exceptions->contains(
            fn ($e) => is_null($e->start_time) && is_null($e->end_time)
        );
    }

    private function getPartialExceptions(Collection $exceptions): Collection
    {
        return $exceptions->filter(
            fn ($e) => $e->start_time && $e->end_time
        );
    }
}
