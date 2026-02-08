<?php

declare(strict_types=1);

use App\Application\Contracts\EventPublisher;
use App\Application\Contracts\TradeJournalRepository;
use App\Application\UC02_TradeJournal\Commands\ReviewTradeCommand;
use App\Application\UC02_TradeJournal\Handlers\ReviewTradeHandler;
use App\Domain\Journal\Aggregates\TradeRecord;
use App\Domain\Journal\Events\TradeReviewed;
use App\Domain\Journal\ValueObjects\DateRange;
use App\Domain\Journal\ValueObjects\Money;
use App\Domain\Journal\ValueObjects\Quantity;
use App\Domain\Metrics\Events\LearningDataAvailable;

function createTradeRecordForReview(string $id = 'rec-001'): TradeRecord
{
    $record = TradeRecord::create(
        $id,
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

    return $record;
}

it('revisa trade record com outcome e lições', function () {
    $record = createTradeRecordForReview();
    $journalRepo = Mockery::mock(TradeJournalRepository::class);
    $publisher = Mockery::mock(EventPublisher::class);

    $journalRepo->shouldReceive('getById')->with('rec-001')->andReturn($record);
    $journalRepo->shouldReceive('save')->once();
    $publisher->shouldReceive('publish')->twice(); // TradeReviewed + LearningDataAvailable

    $handler = new ReviewTradeHandler($journalRepo, $publisher);
    $handler->handle(new ReviewTradeCommand(
        recordId: 'rec-001',
        grossResult: '900.00',
        netResult: '850.00',
        resultType: 'GAIN',
        realizedRR: '2.50',
        followedPlan: true,
        deviationReason: null,
        emotionalState: 'CONTROLLED',
        keepDoing: 'Seguir o plano de trade',
        improveNextTime: 'Melhorar timing de entrada',
    ));

    expect($record->isReviewed())->toBeTrue();
});

it('publica TradeReviewed e LearningDataAvailable', function () {
    $record = createTradeRecordForReview();
    $journalRepo = Mockery::mock(TradeJournalRepository::class);
    $publisher = Mockery::mock(EventPublisher::class);

    $journalRepo->shouldReceive('getById')->andReturn($record);
    $journalRepo->shouldReceive('save')->once();

    $publishedEvents = [];
    $publisher->shouldReceive('publish')->andReturnUsing(
        function ($event) use (&$publishedEvents) {
            $publishedEvents[] = $event;
        }
    );

    $handler = new ReviewTradeHandler($journalRepo, $publisher);
    $handler->handle(new ReviewTradeCommand(
        recordId: 'rec-001',
        grossResult: '-300.00',
        netResult: '-350.00',
        resultType: 'LOSS',
        realizedRR: '-1.00',
        followedPlan: false,
        deviationReason: 'Entrei antes do sinal',
        emotionalState: 'IMPULSIVE',
        keepDoing: 'Manter stops',
        improveNextTime: 'Esperar confirmação',
    ));

    expect($publishedEvents)->toHaveCount(2)
        ->and($publishedEvents[0])->toBeInstanceOf(TradeReviewed::class)
        ->and($publishedEvents[1])->toBeInstanceOf(LearningDataAvailable::class);
});

it('rejeita review duplicado', function () {
    $record = createTradeRecordForReview();
    $journalRepo = Mockery::mock(TradeJournalRepository::class);
    $publisher = Mockery::mock(EventPublisher::class);

    // Primeiro review
    $journalRepo->shouldReceive('getById')->andReturn($record);
    $journalRepo->shouldReceive('save')->once();
    $publisher->shouldReceive('publish')->twice();

    $handler = new ReviewTradeHandler($journalRepo, $publisher);
    $handler->handle(new ReviewTradeCommand(
        recordId: 'rec-001',
        grossResult: '900.00',
        netResult: '850.00',
        resultType: 'GAIN',
        realizedRR: '2.50',
        followedPlan: true,
        deviationReason: null,
        emotionalState: 'CONTROLLED',
        keepDoing: 'Manter disciplina',
        improveNextTime: 'Melhorar sizing',
    ));

    // Limpar eventos do primeiro review
    $record->releaseEvents();

    // Segundo review deve falhar
    $handler->handle(new ReviewTradeCommand(
        recordId: 'rec-001',
        grossResult: '100.00',
        netResult: '80.00',
        resultType: 'GAIN',
        realizedRR: '1.00',
        followedPlan: true,
        deviationReason: null,
        emotionalState: 'CONTROLLED',
        keepDoing: 'Manter',
        improveNextTime: 'Melhorar',
    ));
})->throws(\DomainException::class, 'already reviewed');
