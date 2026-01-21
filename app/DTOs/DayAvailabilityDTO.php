<?php

namespace App\DTOs;

class DayAvailabilityDTO {
    /**
    * @param SlotDTO[] $slot
    */
    public function __construct(
        public readonly string $date,
        public readonly int $dayOfWeek,
        public readonly bool $available,
        public readonly ?string $reason,
        public readonly array $slots,
    ){}

    public static function fromArray(array $data): self
    {
        return new self(
            date: $data['date'],
            dayOfWeek: $data['day_of_week'],
            available: $data['available'],
            reason: $data['reason'] ?? null,
            slots: array_map(
                fn($slot) => SlotDTO::fromArray($slot),
                $data['slots'] ?? []
            )
        );
    }

    public function toArray(): array
    {
        return [
            'date'        => $this->date,
            'day_of_week' => $this->dayOfWeek,
            'available'   => $this->available,
            'reason'      => $this->reason,
            'slots'       => $this->slots
        ];
    }
}