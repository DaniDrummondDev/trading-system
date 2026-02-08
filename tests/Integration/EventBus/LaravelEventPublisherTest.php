<?php

declare(strict_types=1);

use App\Domain\Trade\Events\TradeCreated;
use App\Infrastructure\EventBus\LaravelEventPublisher;
use App\Infrastructure\Persistence\Eloquent\DomainEventModel;

it('persiste evento no Event Store', function () {
    $publisher = new LaravelEventPublisher;
    $event = new TradeCreated('trade-001', 'PETR4', 'LONG', 'D1');

    $publisher->publish($event);

    $stored = DomainEventModel::where('event_id', $event->eventId())->first();

    expect($stored)->not->toBeNull()
        ->and($stored->event_type)->toBe(TradeCreated::class)
        ->and($stored->aggregate_id)->toBe('trade-001')
        ->and($stored->aggregate_type)->toBe('TradeAggregate');
});

it('serializa payload corretamente em JSONB', function () {
    $publisher = new LaravelEventPublisher;
    $event = new TradeCreated('trade-002', 'VALE3', 'SHORT', 'H4');

    $publisher->publish($event);

    $stored = DomainEventModel::where('event_id', $event->eventId())->first();
    $payload = $stored->payload;

    expect($payload)->toBeArray()
        ->and($payload['tradeId'])->toBe('trade-002')
        ->and($payload['assetSymbol'])->toBe('VALE3')
        ->and($payload['direction'])->toBe('SHORT')
        ->and($payload['timeframe'])->toBe('H4');
});

it('persiste múltiplos eventos do mesmo agregado', function () {
    $publisher = new LaravelEventPublisher;

    $event1 = new TradeCreated('trade-003', 'PETR4', 'LONG', 'D1');
    $event2 = new TradeCreated('trade-003', 'PETR4', 'LONG', 'D1');

    $publisher->publish($event1);
    $publisher->publish($event2);

    $count = DomainEventModel::where('aggregate_id', 'trade-003')->count();

    expect($count)->toBe(2);
});

it('despacha evento via Laravel event system', function () {
    $dispatched = [];
    Event::listen(TradeCreated::class, function ($event) use (&$dispatched) {
        $dispatched[] = $event;
    });

    $publisher = new LaravelEventPublisher;
    $event = new TradeCreated('trade-004', 'BBDC4', 'LONG', 'D1');
    $publisher->publish($event);

    expect($dispatched)->toHaveCount(1)
        ->and($dispatched[0])->toBeInstanceOf(TradeCreated::class)
        ->and($dispatched[0]->tradeId())->toBe('trade-004');
});
