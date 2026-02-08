<?php

declare(strict_types=1);

use App\Domain\Shared\AggregateRoot;
use App\Domain\Shared\Events\DomainEvent;

final class ConcreteAggregate extends AggregateRoot
{
    public function doSomething(DomainEvent $event): void
    {
        $this->recordEvent($event);
    }
}

final class FakeDomainEvent implements DomainEvent
{
    public function __construct(
        private readonly string $aggregateId,
    ) {}

    public function eventId(): string
    {
        return 'evt-001';
    }

    public function occurredOn(): \DateTimeImmutable
    {
        return new \DateTimeImmutable('2025-01-01 00:00:00');
    }

    public function aggregateId(): string
    {
        return $this->aggregateId;
    }
}

it('inicia sem eventos registrados', function () {
    $aggregate = new ConcreteAggregate('agg-001');

    expect($aggregate->releaseEvents())->toBeEmpty();
});

it('registra e libera eventos', function () {
    $aggregate = new ConcreteAggregate('agg-001');
    $event = new FakeDomainEvent('agg-001');

    $aggregate->doSomething($event);

    $events = $aggregate->releaseEvents();

    expect($events)->toHaveCount(1)
        ->and($events[0])->toBe($event);
});

it('limpa eventos após release', function () {
    $aggregate = new ConcreteAggregate('agg-001');
    $event = new FakeDomainEvent('agg-001');

    $aggregate->doSomething($event);
    $aggregate->releaseEvents();

    expect($aggregate->releaseEvents())->toBeEmpty();
});

it('registra múltiplos eventos na ordem correta', function () {
    $aggregate = new ConcreteAggregate('agg-001');
    $event1 = new FakeDomainEvent('agg-001');
    $event2 = new FakeDomainEvent('agg-001');

    $aggregate->doSomething($event1);
    $aggregate->doSomething($event2);

    $events = $aggregate->releaseEvents();

    expect($events)->toHaveCount(2)
        ->and($events[0])->toBe($event1)
        ->and($events[1])->toBe($event2);
});
