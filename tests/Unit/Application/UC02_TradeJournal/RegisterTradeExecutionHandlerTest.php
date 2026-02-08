<?php

declare(strict_types=1);

use App\Application\Contracts\EventPublisher;
use App\Application\Contracts\TradeJournalRepository;
use App\Application\Contracts\TradeRepository;
use App\Application\Contracts\UuidGenerator;
use App\Application\UC02_TradeJournal\Commands\RegisterTradeExecutionCommand;
use App\Application\UC02_TradeJournal\DTOs\TradeRecordCreatedDTO;
use App\Application\UC02_TradeJournal\Handlers\RegisterTradeExecutionHandler;
use App\Domain\Journal\Aggregates\TradeRecord;
use App\Domain\Journal\Events\TradeRecordCreated;
use App\Domain\Trade\Aggregates\TradeAggregate;
use App\Domain\Trade\ValueObjects\Asset;
use App\Domain\Trade\ValueObjects\Price;
use App\Domain\Trade\ValueObjects\PriceLevel;
use App\Domain\Trade\ValueObjects\PriceLevelType;
use App\Domain\Trade\ValueObjects\Timeframe;
use App\Domain\Trade\ValueObjects\TradeDirection;

function createExecutedTradeForJournal(string $id = 'trade-001'): TradeAggregate
{
    $trade = TradeAggregate::create($id, new Asset('PETR4', 'B3'), TradeDirection::LONG, Timeframe::D1);
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

it('registra execução no journal quando trade está em EXECUTED', function () {
    $tradeRepo = Mockery::mock(TradeRepository::class);
    $journalRepo = Mockery::mock(TradeJournalRepository::class);
    $publisher = Mockery::mock(EventPublisher::class);
    $uuidGen = Mockery::mock(UuidGenerator::class);

    $tradeRepo->shouldReceive('getById')->with('trade-001')->andReturn(createExecutedTradeForJournal());
    $uuidGen->shouldReceive('generate')->once()->andReturn('rec-001');
    $journalRepo->shouldReceive('save')->once()->with(Mockery::type(TradeRecord::class));
    $publisher->shouldReceive('publish')->once();

    $handler = new RegisterTradeExecutionHandler($tradeRepo, $journalRepo, $publisher, $uuidGen);

    $result = $handler->handle(new RegisterTradeExecutionCommand(
        tradeId: 'trade-001',
        entryPrice: '25.40',
        currency: 'BRL',
        quantity: 200,
        entryDate: '2026-01-10',
        exitDate: '2026-01-15',
    ));

    expect($result)->toBeInstanceOf(TradeRecordCreatedDTO::class)
        ->and($result->tradeRecordId)->toBe('rec-001');
});

it('publica evento TradeRecordCreated', function () {
    $tradeRepo = Mockery::mock(TradeRepository::class);
    $journalRepo = Mockery::mock(TradeJournalRepository::class);
    $publisher = Mockery::mock(EventPublisher::class);
    $uuidGen = Mockery::mock(UuidGenerator::class);

    $tradeRepo->shouldReceive('getById')->andReturn(createExecutedTradeForJournal());
    $uuidGen->shouldReceive('generate')->andReturn('rec-002');
    $journalRepo->shouldReceive('save')->once();

    $publishedEvents = [];
    $publisher->shouldReceive('publish')->andReturnUsing(
        function ($event) use (&$publishedEvents) {
            $publishedEvents[] = $event;
        }
    );

    $handler = new RegisterTradeExecutionHandler($tradeRepo, $journalRepo, $publisher, $uuidGen);
    $handler->handle(new RegisterTradeExecutionCommand(
        tradeId: 'trade-001',
        entryPrice: '25.40',
        currency: 'BRL',
        quantity: 200,
        entryDate: '2026-01-10',
        exitDate: '2026-01-15',
    ));

    expect($publishedEvents)->toHaveCount(1)
        ->and($publishedEvents[0])->toBeInstanceOf(TradeRecordCreated::class)
        ->and($publishedEvents[0]->tradeId())->toBe('trade-001');
});

it('rejeita registro quando trade não está em EXECUTED', function () {
    $trade = TradeAggregate::create('trade-002', new Asset('VALE3', 'B3'), TradeDirection::LONG, Timeframe::D1);
    $trade->analyze(
        new PriceLevel(new Price('60.00'), PriceLevelType::ENTRY),
        new PriceLevel(new Price('58.00'), PriceLevelType::STOP),
        new PriceLevel(new Price('65.00'), PriceLevelType::TARGET),
    );
    $trade->validateRisk('1.0', 100);
    $trade->approve();
    $trade->releaseEvents();

    $tradeRepo = Mockery::mock(TradeRepository::class);
    $journalRepo = Mockery::mock(TradeJournalRepository::class);
    $publisher = Mockery::mock(EventPublisher::class);
    $uuidGen = Mockery::mock(UuidGenerator::class);

    $tradeRepo->shouldReceive('getById')->with('trade-002')->andReturn($trade);

    $handler = new RegisterTradeExecutionHandler($tradeRepo, $journalRepo, $publisher, $uuidGen);

    $handler->handle(new RegisterTradeExecutionCommand(
        tradeId: 'trade-002',
        entryPrice: '60.00',
        currency: 'BRL',
        quantity: 100,
        entryDate: '2026-01-10',
        exitDate: '2026-01-15',
    ));
})->throws(\DomainException::class, 'Trade must be in EXECUTED state');
