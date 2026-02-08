<?php

declare(strict_types=1);

namespace App\Domain\Shared\Events;

interface DomainEvent
{
    public function eventId(): string;

    public function occurredOn(): \DateTimeImmutable;

    public function aggregateId(): string;
}
