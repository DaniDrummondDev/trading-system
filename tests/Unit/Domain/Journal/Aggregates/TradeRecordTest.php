<?php

declare(strict_types=1);

use App\Domain\Journal\Aggregates\TradeRecord;
use App\Domain\Journal\Events\TradeRecordCreated;
use App\Domain\Journal\Events\TradeReviewed;
use App\Domain\Journal\ValueObjects\DateRange;
use App\Domain\Journal\ValueObjects\EmotionalState;
use App\Domain\Journal\ValueObjects\Money;
use App\Domain\Journal\ValueObjects\Quantity;
use App\Domain\Journal\ValueObjects\ResultType;
use App\Domain\Journal\ValueObjects\TradeLesson;
use App\Domain\Journal\ValueObjects\TradeOutcome;
use App\Domain\Journal\ValueObjects\TradeRationale;
use App\Domain\Metrics\Events\LearningDataAvailable;

function createTradeRecord(string $id = 'rec-001'): TradeRecord
{
    return TradeRecord::create(
        $id,
        'user-001',
        'trade-001',
        'PETR4',
        new Money('25.50'),
        new Quantity(200),
        new DateRange(
            new \DateTimeImmutable('2025-01-10'),
            new \DateTimeImmutable('2025-01-15'),
        ),
    );
}

function makeOutcome(ResultType $type = ResultType::GAIN, string $gross = '1200.00'): TradeOutcome
{
    return new TradeOutcome(
        new Money($gross),
        new Money(bcsub($gross, '50.00', 8)),
        $type,
        '2.5',
    );
}

function makeLesson(): TradeLesson
{
    return new TradeLesson(
        'Respeitou o stop loss',
        'Esperar confirmação no candle de entrada',
    );
}

// --- Criação ---

it('cria trade record com evento TradeRecordCreated', function () {
    $record = createTradeRecord();

    expect($record->isReviewed())->toBeFalse()
        ->and($record->outcome())->toBeNull()
        ->and($record->lesson())->toBeNull();

    $events = $record->releaseEvents();
    expect($events)->toHaveCount(1)
        ->and($events[0])->toBeInstanceOf(TradeRecordCreated::class)
        ->and($events[0]->tradeId())->toBe('trade-001')
        ->and($events[0]->assetSymbol())->toBe('PETR4');
});

// --- Review ---

it('review completa gera TradeReviewed e LearningDataAvailable', function () {
    $record = createTradeRecord();
    $record->releaseEvents();

    $record->review(
        makeOutcome(),
        new TradeRationale(followedPlan: true),
        EmotionalState::CONTROLLED,
        makeLesson(),
    );

    expect($record->isReviewed())->toBeTrue()
        ->and($record->outcome())->not->toBeNull()
        ->and($record->lesson())->not->toBeNull();

    $events = $record->releaseEvents();
    expect($events)->toHaveCount(2)
        ->and($events[0])->toBeInstanceOf(TradeReviewed::class)
        ->and($events[1])->toBeInstanceOf(LearningDataAvailable::class);
});

it('não permite review duplicada', function () {
    $record = createTradeRecord();

    $record->review(
        makeOutcome(),
        new TradeRationale(followedPlan: true),
        EmotionalState::CONTROLLED,
        makeLesson(),
    );

    $record->review(
        makeOutcome(),
        new TradeRationale(followedPlan: true),
        EmotionalState::CONTROLLED,
        makeLesson(),
    );
})->throws(\DomainException::class, 'Trade record already reviewed.');

// --- Attention Flag ---

it('ativa attention flag quando emoção divergente + loss', function () {
    $record = createTradeRecord();

    $record->review(
        makeOutcome(ResultType::LOSS, '-500.00'),
        new TradeRationale(followedPlan: true),
        EmotionalState::IMPULSIVE,
        makeLesson(),
    );

    expect($record->hasAttentionFlag())->toBeTrue();
});

it('não ativa attention flag quando emoção controlada + loss', function () {
    $record = createTradeRecord();

    $record->review(
        makeOutcome(ResultType::LOSS, '-500.00'),
        new TradeRationale(followedPlan: true),
        EmotionalState::CONTROLLED,
        makeLesson(),
    );

    expect($record->hasAttentionFlag())->toBeFalse();
});

it('não ativa attention flag quando emoção divergente + gain', function () {
    $record = createTradeRecord();

    $record->review(
        makeOutcome(ResultType::GAIN, '1200.00'),
        new TradeRationale(followedPlan: true),
        EmotionalState::OVERCONFIDENT,
        makeLesson(),
    );

    expect($record->hasAttentionFlag())->toBeFalse();
});

it('não ativa attention flag quando não revisado', function () {
    $record = createTradeRecord();

    expect($record->hasAttentionFlag())->toBeFalse();
});

// --- Setup Invalidation ---

it('invalida setup quando não seguiu o plano', function () {
    $record = createTradeRecord();

    $record->review(
        makeOutcome(),
        new TradeRationale(followedPlan: false, deviationReason: 'Entrou antes do sinal'),
        EmotionalState::ANXIOUS,
        makeLesson(),
    );

    expect($record->hasSetupInvalidation())->toBeTrue();
});

it('não invalida setup quando seguiu o plano', function () {
    $record = createTradeRecord();

    $record->review(
        makeOutcome(),
        new TradeRationale(followedPlan: true),
        EmotionalState::CONTROLLED,
        makeLesson(),
    );

    expect($record->hasSetupInvalidation())->toBeFalse();
});

// --- Journal data ---

it('expõe dados do journal via aggregate', function () {
    $record = createTradeRecord();

    $journal = $record->journal();

    expect($journal->tradeId())->toBe('trade-001')
        ->and($journal->assetSymbol())->toBe('PETR4')
        ->and($journal->entryPrice()->amount())->toBe('25.50')
        ->and($journal->quantity()->value())->toBe(200)
        ->and($journal->tradePeriod()->durationInDays())->toBe(5);
});
