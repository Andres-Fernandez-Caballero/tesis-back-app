<?php

namespace App\DTOs;

class SlotDTO {
    public function __construct(
        public readonly string $start,
        public readonly string $end,
    )
    {}

    public static function fromArray(array $data): self
    {
        return new self(
            start: $data['start'],
            end: $data['end'],
        );
    }

    public function toArray(): array
    {
        return [
            'start' => $this->start,
            'end' => $this->end,
        ];
    }
}