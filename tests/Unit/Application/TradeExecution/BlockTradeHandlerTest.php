<?php

declare(strict_types=1);

use App\Application\Contracts\EventPublisher;
use App\Application\Contracts\TradeRepository;
use App\Application\TradeExecution\Commands\BlockTradeCommand;
use App\Application\TradeExecution\Handlers\BlockTradeHandler;
use App\Domain\Trade\Aggregates\TradeAggregate;
use App\Domain\Trade\Events\TradeBlocked;
use App\Domain\Trade\ValueObjects\Asset;
use App\Domain\Trade\ValueObjects\Price;
use App\Domain\Trade\ValueObjects\PriceLevel;
use App\Domain\Trade\ValueObjects\PriceLevelType;
use App\Domain\Trade\ValueObjects\Timeframe;
use App\Domain\Trade\ValueObjects\TradeDirection;
use App\Domain\Trade\ValueObjects\TradeState;

function createAnalyzedTradeForBlock(string $id = 'trade-001'): TradeAggregate
{
    $trade = TradeAggregate::create($id, 'user-001', new Asset('PETR4', 'B3'), TradeDirection::LONG, Timeframe::D1);
    $trade->analyze(
        new PriceLevel(new Price('25.50'), PriceLevelType::ENTRY),
        new PriceLevel(new Price('24.00'), PriceLevelType::STOP),
        new PriceLevel(new Price('30.00'), PriceLevelType::TARGET),
    );
    $trade->releaseEvents();

    return $trade;
}

it('bloqueia trade com razões', function () {
    $trade = createAnalyzedTradeForBlock();
    $repository = Mockery::mock(TradeRepository::class);
    $publisher = Mockery::mock(EventPublisher::class);

    $repository->shouldReceive('getById')->with('trade-001')->andReturn($trade);
    $repository->shouldReceive('save')->once();
    $publisher->shouldReceive('publish')->once();

    $handler = new BlockTradeHandler($repository, $publisher);
    $handler->handle(new BlockTradeCommand(
        tradeId: 'trade-001',
        reasons: [
            ['code' => 'RISK_EXCEEDED', 'description' => 'Risco acima do limite'],
        ],
    ));

    expect($trade->state())->toBe(TradeState::BLOCKED);
});

it('publica evento TradeBlocked com razões', function () {
    $trade = createAnalyzedTradeForBlock();
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

    $handler = new BlockTradeHandler($repository, $publisher);
    $handler->handle(new BlockTradeCommand(
        tradeId: 'trade-001',
        reasons: [
            ['code' => 'RISK_EXCEEDED', 'description' => 'Risco acima do limite'],
            ['code' => 'EXPOSURE_LIMIT', 'description' => 'Exposição máxima atingida'],
        ],
    ));

    expect($publishedEvents)->toHaveCount(1)
        ->and($publishedEvents[0])->toBeInstanceOf(TradeBlocked::class)
        ->and($publishedEvents[0]->reasons())->toHaveCount(2);
});
