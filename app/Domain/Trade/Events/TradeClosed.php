<?php

declare(strict_types=1);

namespace App\Domain\Trade\Events;

use App\Domain\Shared\Events\DomainEvent;

class TradeClosed implements DomainEvent
{
    public function __construct(
        public readonly string $tradeId,
        public readonly float $result,
    ) {}

    public function occurredOn(): \DateTimeImmutable
    {
        return new \DateTimeImmutable;
    }
}
