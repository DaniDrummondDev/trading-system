<?php

declare(strict_types=1);

namespace App\Infrastructure\EventBus;

use App\Application\Contracts\EventPublisher;
use App\Domain\Shared\Events\DomainEvent;
use App\Infrastructure\Persistence\Eloquent\DomainEventModel;

final class LaravelEventPublisher implements EventPublisher
{
    public function publish(DomainEvent $event): void
    {
        DomainEventModel::create([
            'event_id' => $event->eventId(),
            'event_type' => $event::class,
            'aggregate_id' => $event->aggregateId(),
            'aggregate_type' => $this->resolveAggregateType($event),
            'payload' => $this->serializePayload($event),
            'occurred_on' => $event->occurredOn(),
            'created_at' => now(),
        ]);

        event($event);
    }

    private function resolveAggregateType(DomainEvent $event): string
    {
        $namespace = (new \ReflectionClass($event))->getNamespaceName();

        return match (true) {
            str_contains($namespace, 'Trade') => 'TradeAggregate',
            str_contains($namespace, 'Journal') => 'TradeRecord',
            str_contains($namespace, 'Metrics') => 'TraderMetrics',
            default => 'Unknown',
        };
    }

    /** @return array<string, mixed> */
    private function serializePayload(DomainEvent $event): array
    {
        $payload = [];
        $reflection = new \ReflectionClass($event);

        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->getNumberOfParameters() > 0) {
                continue;
            }

            $name = $method->getName();

            if (in_array($name, ['eventId', 'occurredOn', 'aggregateId', '__construct'], true)) {
                continue;
            }

            $value = $event->{$name}();

            if ($value instanceof \DateTimeImmutable) {
                $value = $value->format('c');
            }

            $payload[$name] = $value;
        }

        return $payload;
    }
}
