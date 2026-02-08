<?php

declare(strict_types=1);

namespace App\Domain\Shared;

use App\Domain\Shared\Events\DomainEvent;

abstract class AggregateRoot extends Entity
{
    /** @var DomainEvent[] */
    private array $domainEvents = [];

    protected function recordEvent(DomainEvent $event): void
    {
        $this->domainEvents[] = $event;
    }

    /**
     * Libera os eventos registrados e limpa a lista interna.
     *
     * @return DomainEvent[]
     */
    public function releaseEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];

        return $events;
    }
}
