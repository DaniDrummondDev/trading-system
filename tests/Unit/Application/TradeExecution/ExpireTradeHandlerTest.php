<?php

declare(strict_types=1);

use App\Application\Contracts\EventPublisher;
use App\Application\Contracts\TradeRepository;
use App\Application\TradeExecution\Commands\ExpireTradeCommand;
use App\Application\TradeExecution\Handlers\ExpireTradeHandler;
use App\Domain\Trade\Aggregates\TradeAggregate;
use App\Domain\Trade\Events\TradeExpired;
use App\Domain\Trade\ValueObjects\Asset;
use App\Domain\Trade\ValueObjects\Price;
use App\Domain\Trade\ValueObjects\PriceLevel;
use App\Domain\Trade\ValueObjects\PriceLevelType;
use App\Domain\Trade\ValueObjects\Timeframe;
use App\Domain\Trade\ValueObjects\TradeDirection;
use App\Domain\Trade\ValueObjects\TradeState;

function createApprovedTradeForExpire(string $id = 'trade-001'): TradeAggregate
{
    $trade = TradeAggregate::create($id, 'user-001', new Asset('PETR4', 'B3'), TradeDirection::LONG, Timeframe::D1);
    $trade->analyze(
        new PriceLevel(new Price('25.50'), PriceLevelType::ENTRY),
        new PriceLevel(new Price('24.00'), PriceLevelType::STOP),
        new PriceLevel(new Price('30.00'), PriceLevelType::TARGET),
    );
    $trade->validateRisk('1.5', 200);
    $trade->approve();
    $trade->releaseEvents();

    return $trade;
}

it('expira trade aprovado', function () {
    $trade = createApprovedTradeForExpire();
    $repository = Mockery::mock(TradeRepository::class);
    $publisher = Mockery::mock(EventPublisher::class);

    $repository->shouldReceive('getById')->with('trade-001')->andReturn($trade);
    $repository->shouldReceive('save')->once();
    $publisher->shouldReceive('publish')->once();

    $handler = new ExpireTradeHandler($repository, $publisher);
    $handler->handle(new ExpireTradeCommand(
        tradeId: 'trade-001',
        reason: 'Prazo expirado sem execução',
    ));

    expect($trade->state())->toBe(TradeState::EXPIRED);
});

it('publica evento TradeExpired', function () {
    $trade = createApprovedTradeForExpire();
    $repository = Mockery::mock(TradeRepository::class);
    $publisher = Mockery::mock(EventPublisher::class);

    $repository->shouldReceive('getById')->andReturn($trade);
    $repository->shouldReceive('save')->once();

    $publishedEvents = [];
    $publisher->shouldReceive('publish')->andReturnUsing(
        function ($event) use (&$publishedEvents) {
            $publishedEvents[] = $event;
        }
    );

    $handler = new ExpireTradeHandler($repository, $publisher);
    $handler->handle(new ExpireTradeCommand(
        tradeId: 'trade-001',
        reason: 'Prazo expirado',
    ));

    expect($publishedEvents)->toHaveCount(1)
        ->and($publishedEvents[0])->toBeInstanceOf(TradeExpired::class)
        ->and($publishedEvents[0]->reason())->toBe('Prazo expirado');
});
