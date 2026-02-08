<?php

declare(strict_types=1);

use App\Application\Contracts\EventPublisher;
use App\Application\Contracts\TradeRepository;
use App\Application\UC01_TradeExecution\Commands\ValidateTradeRiskCommand;
use App\Application\UC01_TradeExecution\Handlers\ValidateTradeRiskHandler;
use App\Domain\Trade\Aggregates\TradeAggregate;
use App\Domain\Trade\Events\TradeRiskValidated;
use App\Domain\Trade\ValueObjects\Asset;
use App\Domain\Trade\ValueObjects\Price;
use App\Domain\Trade\ValueObjects\PriceLevel;
use App\Domain\Trade\ValueObjects\PriceLevelType;
use App\Domain\Trade\ValueObjects\Timeframe;
use App\Domain\Trade\ValueObjects\TradeDirection;
use App\Domain\Trade\ValueObjects\TradeState;

function createAnalyzedTrade(string $id = 'trade-001'): TradeAggregate
{
    $trade = TradeAggregate::create($id, new Asset('PETR4', 'B3'), TradeDirection::LONG, Timeframe::D1);
    $trade->analyze(
        new PriceLevel(new Price('25.50'), PriceLevelType::ENTRY),
        new PriceLevel(new Price('24.00'), PriceLevelType::STOP),
        new PriceLevel(new Price('30.00'), PriceLevelType::TARGET),
    );
    $trade->releaseEvents();

    return $trade;
}

it('valida risco do trade', function () {
    $trade = createAnalyzedTrade();
    $repository = Mockery::mock(TradeRepository::class);
    $publisher = Mockery::mock(EventPublisher::class);

    $repository->shouldReceive('getById')->with('trade-001')->andReturn($trade);
    $repository->shouldReceive('save')->once();
    $publisher->shouldReceive('publish')->once();

    $handler = new ValidateTradeRiskHandler($repository, $publisher);
    $handler->handle(new ValidateTradeRiskCommand(
        tradeId: 'trade-001',
        riskPercentage: '1.5',
        positionSize: 200,
    ));

    expect($trade->state())->toBe(TradeState::RISK_VALIDATED);
});

it('publica evento TradeRiskValidated', function () {
    $trade = createAnalyzedTrade();
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

    $handler = new ValidateTradeRiskHandler($repository, $publisher);
    $handler->handle(new ValidateTradeRiskCommand(
        tradeId: 'trade-001',
        riskPercentage: '1.5',
        positionSize: 200,
    ));

    expect($publishedEvents)->toHaveCount(1)
        ->and($publishedEvents[0])->toBeInstanceOf(TradeRiskValidated::class)
        ->and($publishedEvents[0]->riskPercentage())->toBe('1.5')
        ->and($publishedEvents[0]->positionSize())->toBe(200);
});
