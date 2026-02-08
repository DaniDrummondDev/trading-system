<?php

declare(strict_types=1);

use App\Application\Contracts\TradeJournalRepository;
use App\Application\TradeJournal\DTOs\TradeRecordViewDTO;
use App\Application\TradeJournal\Handlers\GetTradeRecordByIdHandler;
use App\Application\TradeJournal\Handlers\GetTradeRecordByTradeIdHandler;
use App\Application\TradeJournal\Queries\GetTradeRecordByIdQuery;
use App\Application\TradeJournal\Queries\GetTradeRecordByTradeIdQuery;
use App\Domain\Journal\Aggregates\TradeRecord;
use App\Domain\Journal\ValueObjects\DateRange;
use App\Domain\Journal\ValueObjects\Money;
use App\Domain\Journal\ValueObjects\Quantity;

function createTradeRecordForQuery(string $id = 'rec-001', string $tradeId = 'trade-001'): TradeRecord
{
    $record = TradeRecord::create(
        $id,
        'user-001',
        $tradeId,
        'PETR4',
        new Money('25.40'),
        new Quantity(200),
        new DateRange(
            new \DateTimeImmutable('2026-01-10'),
            new \DateTimeImmutable('2026-01-15'),
        ),
    );
    $record->releaseEvents();

    return $record;
}

it('retorna trade record por ID', function () {
    $record = createTradeRecordForQuery();
    $journalRepo = Mockery::mock(TradeJournalRepository::class);
    $journalRepo->shouldReceive('getById')->with('rec-001')->andReturn($record);

    $handler = new GetTradeRecordByIdHandler($journalRepo);
    $result = $handler->handle(new GetTradeRecordByIdQuery(recordId: 'rec-001'));

    expect($result)->toBeInstanceOf(TradeRecordViewDTO::class)
        ->and($result->id)->toBe('rec-001')
        ->and($result->tradeId)->toBe('trade-001')
        ->and($result->isReviewed)->toBeFalse();
});

it('retorna trade record por trade ID', function () {
    $record = createTradeRecordForQuery();
    $journalRepo = Mockery::mock(TradeJournalRepository::class);
    $journalRepo->shouldReceive('getByTradeId')->with('trade-001')->andReturn($record);

    $handler = new GetTradeRecordByTradeIdHandler($journalRepo);
    $result = $handler->handle(new GetTradeRecordByTradeIdQuery(tradeId: 'trade-001'));

    expect($result)->toBeInstanceOf(TradeRecordViewDTO::class)
        ->and($result->tradeId)->toBe('trade-001')
        ->and($result->assetSymbol)->toBe('PETR4');
});
