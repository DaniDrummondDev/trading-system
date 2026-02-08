<?php

declare(strict_types=1);

use App\Application\Contracts\TradeJournalRepository;
use App\Application\TradeJournal\DTOs\TradeRecordViewDTO;
use App\Application\TradeJournal\Handlers\GetTradeJournalHandler;
use App\Application\TradeJournal\Queries\GetTradeJournalQuery;
use App\Domain\Journal\Aggregates\TradeRecord;
use App\Domain\Journal\ValueObjects\DateRange;
use App\Domain\Journal\ValueObjects\Money;
use App\Domain\Journal\ValueObjects\Quantity;

it('retorna lista de trade records do usuário', function () {
    $record = TradeRecord::create(
        'rec-001',
        'user-001',
        'trade-001',
        'PETR4',
        new Money('25.40'),
        new Quantity(200),
        new DateRange(
            new \DateTimeImmutable('2026-01-10'),
            new \DateTimeImmutable('2026-01-15'),
        ),
    );
    $record->releaseEvents();

    $journalRepo = Mockery::mock(TradeJournalRepository::class);
    $journalRepo->shouldReceive('getByUserId')
        ->with('user-001', Mockery::any())
        ->andReturn([$record]);

    $handler = new GetTradeJournalHandler($journalRepo);
    $result = $handler->handle(new GetTradeJournalQuery(
        userId: 'user-001',
        periodStart: '2026-01-01',
        periodEnd: '2026-01-31',
    ));

    expect($result)->toHaveCount(1)
        ->and($result[0])->toBeInstanceOf(TradeRecordViewDTO::class)
        ->and($result[0]->tradeId)->toBe('trade-001')
        ->and($result[0]->assetSymbol)->toBe('PETR4');
});

it('retorna lista vazia quando não há records', function () {
    $journalRepo = Mockery::mock(TradeJournalRepository::class);
    $journalRepo->shouldReceive('getByUserId')
        ->with('user-001', null)
        ->andReturn([]);

    $handler = new GetTradeJournalHandler($journalRepo);
    $result = $handler->handle(new GetTradeJournalQuery(userId: 'user-001'));

    expect($result)->toBeArray()->toBeEmpty();
});
