<?php

declare(strict_types=1);

use App\Domain\Trade\Events\TradeAnalyzed;
use App\Domain\Trade\Events\TradeApproved;
use App\Domain\Trade\Events\TradeBlocked;
use App\Domain\Trade\Events\TradeClosed;
use App\Domain\Trade\Events\TradeCreated;
use App\Domain\Trade\Events\TradeExecuted;
use App\Domain\Trade\Events\TradeExpired;
use App\Domain\Trade\Events\TradeRiskValidated;

it('TradeCreated carrega payload correto', function () {
    $event = new TradeCreated('trade-001', 'PETR4', 'LONG', 'D1');

    expect($event->tradeId())->toBe('trade-001')
        ->and($event->assetSymbol())->toBe('PETR4')
        ->and($event->direction())->toBe('LONG')
        ->and($event->timeframe())->toBe('D1')
        ->and($event->aggregateId())->toBe('trade-001')
        ->and($event->eventId())->toBeString()->not->toBeEmpty()
        ->and($event->occurredOn())->toBeInstanceOf(\DateTimeImmutable::class);
});

it('TradeAnalyzed carrega níveis de preço', function () {
    $event = new TradeAnalyzed('trade-001', '25.50', '24.00', '30.00');

    expect($event->tradeId())->toBe('trade-001')
        ->and($event->entryPrice())->toBe('25.50')
        ->and($event->stopPrice())->toBe('24.00')
        ->and($event->targetPrice())->toBe('30.00');
});

it('TradeRiskValidated carrega dados de risco', function () {
    $event = new TradeRiskValidated('trade-001', '1.5', 200);

    expect($event->tradeId())->toBe('trade-001')
        ->and($event->riskPercentage())->toBe('1.5')
        ->and($event->positionSize())->toBe(200);
});

it('TradeApproved carrega tradeId', function () {
    $event = new TradeApproved('trade-001');

    expect($event->tradeId())->toBe('trade-001')
        ->and($event->aggregateId())->toBe('trade-001');
});

it('TradeBlocked carrega razões', function () {
    $reasons = ['Risco excedido', 'Drawdown máximo atingido'];
    $event = new TradeBlocked('trade-001', $reasons);

    expect($event->tradeId())->toBe('trade-001')
        ->and($event->reasons())->toBe($reasons);
});

it('TradeExecuted carrega preço e quantidade', function () {
    $event = new TradeExecuted('trade-001', '25.30', 100);

    expect($event->tradeId())->toBe('trade-001')
        ->and($event->executedPrice())->toBe('25.30')
        ->and($event->quantity())->toBe(100);
});

it('TradeClosed carrega resultado como string', function () {
    $event = new TradeClosed('trade-001', '1500.00');

    expect($event->tradeId())->toBe('trade-001')
        ->and($event->result())->toBe('1500.00');
});

it('TradeExpired carrega razão', function () {
    $event = new TradeExpired('trade-001', 'Oportunidade expirou sem execução');

    expect($event->tradeId())->toBe('trade-001')
        ->and($event->reason())->toBe('Oportunidade expirou sem execução');
});

it('cada evento tem eventId único', function () {
    $event1 = new TradeCreated('trade-001', 'PETR4', 'LONG', 'D1');
    $event2 = new TradeCreated('trade-001', 'PETR4', 'LONG', 'D1');

    expect($event1->eventId())->not->toBe($event2->eventId());
});

it('timestamp é capturado na construção', function () {
    $before = new \DateTimeImmutable;
    $event = new TradeCreated('trade-001', 'PETR4', 'LONG', 'D1');
    $after = new \DateTimeImmutable;

    expect($event->occurredOn() >= $before)->toBeTrue()
        ->and($event->occurredOn() <= $after)->toBeTrue();
});
