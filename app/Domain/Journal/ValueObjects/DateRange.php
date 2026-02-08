<?php

declare(strict_types=1);

namespace App\Domain\Journal\ValueObjects;

use App\Domain\Shared\ValueObject;

final class DateRange extends ValueObject
{
    public function __construct(
        private readonly \DateTimeImmutable $start,
        private readonly \DateTimeImmutable $end,
    ) {
        if ($this->end < $this->start) {
            throw new \InvalidArgumentException('End date must be greater than or equal to start date.');
        }
    }

    public function start(): \DateTimeImmutable
    {
        return $this->start;
    }

    public function end(): \DateTimeImmutable
    {
        return $this->end;
    }

    public function durationInDays(): int
    {
        return (int) $this->start->diff($this->end)->days;
    }

    public function equals(ValueObject $other): bool
    {
        return $other instanceof self
            && $this->start == $other->start
            && $this->end == $other->end;
    }
}
