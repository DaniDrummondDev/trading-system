<?php

declare(strict_types=1);

use App\Application\Contracts\EventPublisher;
use App\Application\Contracts\TradeRepository;
use App\Application\TradeExecution\Commands\ApproveTradeCommand;
use App\Application\TradeExecution\Handlers\ApproveTradeHandler;
use App\Domain\Trade\Aggregates\TradeAggregate;
use App\Domain\Trade\Events\TradeApproved;
use App\Domain\Trade\ValueObjects\Asset;
use App\Domain\Trade\ValueObjects\Price;
use App\Domain\Trade\ValueObjects\PriceLevel;
use App\Domain\Trade\ValueObjects\PriceLevelType;
use App\Domain\Trade\ValueObjects\Timeframe;
use App\Domain\Trade\ValueObjects\TradeDirection;
use App\Domain\Trade\ValueObjects\TradeState;

function createRiskValidatedTrade(string $id = 'trade-001'): TradeAggregate
{
    $trade = TradeAggregate::create($id, 'user-001', new Asset('PETR4', 'B3'), TradeDirection::LONG, Timeframe::D1);
    $trade->analyze(
        new PriceLevel(new Price('25.50'), PriceLevelType::ENTRY),
        new PriceLevel(new Price('24.00'), PriceLevelType::STOP),
        new PriceLevel(new Price('30.00'), PriceLevelType::TARGET),
    );
    $trade->validateRisk('1.5', 200);
    $trade->releaseEvents();

    return $trade;
}

it('aprova trade risk-validated', function () {
    $trade = createRiskValidatedTrade();
    $repository = Mockery::mock(TradeRepository::class);
    $publisher = Mockery::mock(EventPublisher::class);

    $repository->shouldReceive('getById')->with('trade-001')->andReturn($trade);
    $repository->shouldReceive('save')->once();
    $publisher->shouldReceive('publish')->once();

    $handler = new ApproveTradeHandler($repository, $publisher);
    $handler->handle(new ApproveTradeCommand(tradeId: 'trade-001'));

    expect($trade->state())->toBe(TradeState::APPROVED);
});

it('publica evento TradeApproved', function () {
    $trade = createRiskValidatedTrade();
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

    $handler = new ApproveTradeHandler($repository, $publisher);
    $handler->handle(new ApproveTradeCommand(tradeId: 'trade-001'));

    expect($publishedEvents)->toHaveCount(1)
        ->and($publishedEvents[0])->toBeInstanceOf(TradeApproved::class)
        ->and($publishedEvents[0]->tradeId())->toBe('trade-001');
});
