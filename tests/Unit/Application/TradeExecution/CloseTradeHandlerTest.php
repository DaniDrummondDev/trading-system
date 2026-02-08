<?php

declare(strict_types=1);

use App\Application\Contracts\EventPublisher;
use App\Application\Contracts\TradeRepository;
use App\Application\TradeExecution\Commands\CloseTradeCommand;
use App\Application\TradeExecution\Handlers\CloseTradeHandler;
use App\Domain\Trade\Aggregates\TradeAggregate;
use App\Domain\Trade\Events\TradeClosed;
use App\Domain\Trade\ValueObjects\Asset;
use App\Domain\Trade\ValueObjects\Price;
use App\Domain\Trade\ValueObjects\PriceLevel;
use App\Domain\Trade\ValueObjects\PriceLevelType;
use App\Domain\Trade\ValueObjects\Timeframe;
use App\Domain\Trade\ValueObjects\TradeDirection;
use App\Domain\Trade\ValueObjects\TradeState;

function createExecutedTradeForClose(string $id = 'trade-001'): TradeAggregate
{
    $trade = TradeAggregate::create($id, 'user-001', new Asset('PETR4', 'B3'), TradeDirection::LONG, Timeframe::D1);
    $trade->analyze(
        new PriceLevel(new Price('25.50'), PriceLevelType::ENTRY),
        new PriceLevel(new Price('24.00'), PriceLevelType::STOP),
        new PriceLevel(new Price('30.00'), PriceLevelType::TARGET),
    );
    $trade->validateRisk('1.5', 200);
    $trade->approve();
    $trade->execute(new Price('25.40'), 200);
    $trade->releaseEvents();

    return $trade;
}

it('fecha trade executado', function () {
    $trade = createExecutedTradeForClose();
    $repository = Mockery::mock(TradeRepository::class);
    $publisher = Mockery::mock(EventPublisher::class);

    $repository->shouldReceive('getById')->with('trade-001')->andReturn($trade);
    $repository->shouldReceive('save')->once();
    $publisher->shouldReceive('publish')->once();

    $handler = new CloseTradeHandler($repository, $publisher);
    $handler->handle(new CloseTradeCommand(
        tradeId: 'trade-001',
        result: 'GAIN',
    ));

    expect($trade->state())->toBe(TradeState::CLOSED);
});

it('publica evento TradeClosed', function () {
    $trade = createExecutedTradeForClose();
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

    $handler = new CloseTradeHandler($repository, $publisher);
    $handler->handle(new CloseTradeCommand(
        tradeId: 'trade-001',
        result: 'LOSS',
    ));

    expect($publishedEvents)->toHaveCount(1)
        ->and($publishedEvents[0])->toBeInstanceOf(TradeClosed::class)
        ->and($publishedEvents[0]->result())->toBe('LOSS');
});

it('falha ao fechar trade não executado', function () {
    $trade = TradeAggregate::create('trade-002', 'user-001', new Asset('VALE3', 'B3'), TradeDirection::LONG, Timeframe::D1);
    $trade->releaseEvents();

    $repository = Mockery::mock(TradeRepository::class);
    $publisher = Mockery::mock(EventPublisher::class);

    $repository->shouldReceive('getById')->andReturn($trade);

    $handler = new CloseTradeHandler($repository, $publisher);
    $handler->handle(new CloseTradeCommand(
        tradeId: 'trade-002',
        result: 'GAIN',
    ));
})->throws(\DomainException::class);
