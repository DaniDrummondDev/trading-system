<?php

declare(strict_types=1);

namespace App\Domain\Metrics\Events;

use App\Domain\Shared\Events\DomainEvent;

class LearningDataAvailable implements DomainEvent
{
    public function __construct(
        public readonly string $tradeId,
    ) {}

    public function occurredOn(): \DateTimeImmutable
    {
        return new \DateTimeImmutable;
    }
}
