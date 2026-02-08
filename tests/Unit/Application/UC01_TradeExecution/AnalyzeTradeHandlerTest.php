<?php

declare(strict_types=1);

use App\Application\Contracts\EventPublisher;
use App\Application\Contracts\TradeRepository;
use App\Application\UC01_TradeExecution\Commands\AnalyzeTradeCommand;
use App\Application\UC01_TradeExecution\Handlers\AnalyzeTradeHandler;
use App\Domain\Trade\Aggregates\TradeAggregate;
use App\Domain\Trade\Events\TradeAnalyzed;
use App\Domain\Trade\ValueObjects\Asset;
use App\Domain\Trade\ValueObjects\Timeframe;
use App\Domain\Trade\ValueObjects\TradeDirection;
use App\Domain\Trade\ValueObjects\TradeState;

function createTradeForAnalysis(string $id = 'trade-001'): TradeAggregate
{
    $trade = TradeAggregate::create($id, 'user-001', new Asset('PETR4', 'B3'), TradeDirection::LONG, Timeframe::D1);
    $trade->releaseEvents();

    return $trade;
}

it('analisa trade com entry/stop/target', function () {
    $trade = createTradeForAnalysis();
    $repository = Mockery::mock(TradeRepository::class);
    $publisher = Mockery::mock(EventPublisher::class);

    $repository->shouldReceive('getById')->with('trade-001')->andReturn($trade);
    $repository->shouldReceive('save')->once();
    $publisher->shouldReceive('publish')->once();

    $handler = new AnalyzeTradeHandler($repository, $publisher);
    $handler->handle(new AnalyzeTradeCommand(
        tradeId: 'trade-001',
        entryPrice: '25.50',
        stopPrice: '24.00',
        targetPrice: '30.00',
    ));

    expect($trade->state())->toBe(TradeState::ANALYZED);
});

it('publica evento TradeAnalyzed com preços corretos', function () {
    $trade = createTradeForAnalysis();
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

    $handler = new AnalyzeTradeHandler($repository, $publisher);
    $handler->handle(new AnalyzeTradeCommand(
        tradeId: 'trade-001',
        entryPrice: '25.50',
        stopPrice: '24.00',
        targetPrice: '30.00',
    ));

    expect($publishedEvents)->toHaveCount(1)
        ->and($publishedEvents[0])->toBeInstanceOf(TradeAnalyzed::class)
        ->and($publishedEvents[0]->entryPrice())->toBe('25.50')
        ->and($publishedEvents[0]->stopPrice())->toBe('24.00')
        ->and($publishedEvents[0]->targetPrice())->toBe('30.00');
});
