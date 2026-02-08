<?php

declare(strict_types=1);

use App\Application\Contracts\EventPublisher;
use App\Application\Contracts\TradeRepository;
use App\Application\UC01_TradeExecution\Commands\ExecuteTradeCommand;
use App\Application\UC01_TradeExecution\Handlers\ExecuteTradeHandler;
use App\Domain\Trade\Aggregates\TradeAggregate;
use App\Domain\Trade\Events\TradeExecuted;
use App\Domain\Trade\ValueObjects\Asset;
use App\Domain\Trade\ValueObjects\Price;
use App\Domain\Trade\ValueObjects\PriceLevel;
use App\Domain\Trade\ValueObjects\PriceLevelType;
use App\Domain\Trade\ValueObjects\Timeframe;
use App\Domain\Trade\ValueObjects\TradeDirection;
use App\Domain\Trade\ValueObjects\TradeState;

function createApprovedTrade(string $id = 'trade-001'): TradeAggregate
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

it('executa trade aprovado', function () {
    $trade = createApprovedTrade();
    $repository = Mockery::mock(TradeRepository::class);
    $publisher = Mockery::mock(EventPublisher::class);

    $repository->shouldReceive('getById')->with('trade-001')->andReturn($trade);
    $repository->shouldReceive('save')->once();
    $publisher->shouldReceive('publish')->once();

    $handler = new ExecuteTradeHandler($repository, $publisher);
    $handler->handle(new ExecuteTradeCommand(
        tradeId: 'trade-001',
        executedPrice: '25.40',
        quantity: 200,
    ));

    expect($trade->state())->toBe(TradeState::EXECUTED);
});

it('publica evento TradeExecuted com dados corretos', function () {
    $trade = createApprovedTrade();
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

    $handler = new ExecuteTradeHandler($repository, $publisher);
    $handler->handle(new ExecuteTradeCommand(
        tradeId: 'trade-001',
        executedPrice: '25.40',
        quantity: 200,
    ));

    expect($publishedEvents)->toHaveCount(1)
        ->and($publishedEvents[0])->toBeInstanceOf(TradeExecuted::class)
        ->and($publishedEvents[0]->executedPrice())->toBe('25.40')
        ->and($publishedEvents[0]->quantity())->toBe(200);
});

it('falha ao executar trade não aprovado', function () {
    $trade = TradeAggregate::create('trade-002', 'user-001', new Asset('VALE3', 'B3'), TradeDirection::LONG, Timeframe::D1);
    $trade->releaseEvents();

    $repository = Mockery::mock(TradeRepository::class);
    $publisher = Mockery::mock(EventPublisher::class);

    $repository->shouldReceive('getById')->andReturn($trade);

    $handler = new ExecuteTradeHandler($repository, $publisher);
    $handler->handle(new ExecuteTradeCommand(
        tradeId: 'trade-002',
        executedPrice: '60.00',
        quantity: 100,
    ));
})->throws(\DomainException::class);
