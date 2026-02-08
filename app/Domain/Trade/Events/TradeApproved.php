<?php

declare(strict_types=1);

namespace App\Domain\Trade\Events;

use App\Domain\Shared\Events\DomainEvent;

final class TradeApproved implements DomainEvent
{
    private readonly string $eventId;

    private readonly \DateTimeImmutable $occurredOn;

    public function __construct(
        private readonly string $tradeId,
    ) {
        $this->eventId = bin2hex(random_bytes(16));
        $this->occurredOn = new \DateTimeImmutable;
    }

    public function eventId(): string
    {
        return $this->eventId;
    }

    public function occurredOn(): \DateTimeImmutable
    {
        return $this->occurredOn;
    }

    public function aggregateId(): string
    {
        return $this->tradeId;
    }

    public function tradeId(): string
    {
        return $this->tradeId;
    }
}
