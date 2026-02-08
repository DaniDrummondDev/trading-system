<?php

declare(strict_types=1);

use App\Domain\Journal\Aggregates\TradeRecord;
use App\Domain\Journal\ValueObjects\DateRange;
use App\Domain\Journal\ValueObjects\EmotionalState;
use App\Domain\Journal\ValueObjects\Money;
use App\Domain\Journal\ValueObjects\Quantity;
use App\Domain\Journal\ValueObjects\ResultType;
use App\Domain\Journal\ValueObjects\TradeLesson;
use App\Domain\Journal\ValueObjects\TradeOutcome;
use App\Domain\Journal\ValueObjects\TradeRationale;
use App\Infrastructure\Persistence\Repositories\TradeJournalRepositoryEloquent;

function createRecordForPersistence(
    string $id = 'rec-001',
    string $userId = 'user-001',
    string $tradeId = 'trade-001',
): TradeRecord {
    $record = TradeRecord::create(
        $id,
        $userId,
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

it('salva e recupera registro por ID', function () {
    $repo = new TradeJournalRepositoryEloquent;
    $record = createRecordForPersistence();

    $repo->save($record);
    $loaded = $repo->getById('rec-001');

    expect($loaded->id())->toBe('rec-001')
        ->and($loaded->userId())->toBe('user-001')
        ->and($loaded->journal()->tradeId())->toBe('trade-001')
        ->and($loaded->journal()->assetSymbol())->toBe('PETR4')
        ->and($loaded->journal()->entryPrice()->amount())->toBe('25.40000000')
        ->and($loaded->journal()->quantity()->value())->toBe(200)
        ->and($loaded->isReviewed())->toBeFalse();
});

it('recupera registro por trade ID', function () {
    $repo = new TradeJournalRepositoryEloquent;
    $record = createRecordForPersistence('rec-002', 'user-001', 'trade-002');

    $repo->save($record);
    $loaded = $repo->getByTradeId('trade-002');

    expect($loaded->id())->toBe('rec-002')
        ->and($loaded->journal()->tradeId())->toBe('trade-002');
});

it('retorna registros do usuário', function () {
    $repo = new TradeJournalRepositoryEloquent;
    $record = createRecordForPersistence('rec-010', 'user-010', 'trade-010');

    $repo->save($record);
    $records = $repo->getByUserId('user-010');

    expect($records)->toHaveCount(1)
        ->and($records[0]->id())->toBe('rec-010');
});

it('filtra registros por período', function () {
    $repo = new TradeJournalRepositoryEloquent;

    $record1 = createRecordForPersistence('rec-020', 'user-020', 'trade-020');
    $repo->save($record1);

    $record2 = TradeRecord::create(
        'rec-021',
        'user-020',
        'trade-021',
        'VALE3',
        new Money('60.00'),
        new Quantity(100),
        new DateRange(
            new \DateTimeImmutable('2026-03-01'),
            new \DateTimeImmutable('2026-03-10'),
        ),
    );
    $record2->releaseEvents();
    $repo->save($record2);

    $period = new DateRange(
        new \DateTimeImmutable('2026-01-01'),
        new \DateTimeImmutable('2026-01-31'),
    );
    $filtered = $repo->getByUserId('user-020', $period);

    expect($filtered)->toHaveCount(1)
        ->and($filtered[0]->id())->toBe('rec-020');
});

it('salva registro com review completa', function () {
    $repo = new TradeJournalRepositoryEloquent;
    $record = createRecordForPersistence('rec-030', 'user-030', 'trade-030');

    $record->review(
        new TradeOutcome(
            new Money('900.00'),
            new Money('850.00'),
            ResultType::GAIN,
            '2.50',
        ),
        new TradeRationale(true),
        EmotionalState::CONTROLLED,
        new TradeLesson('Seguir o plano', 'Melhorar timing'),
    );
    $record->releaseEvents();
    $repo->save($record);

    $loaded = $repo->getById('rec-030');

    expect($loaded->isReviewed())->toBeTrue()
        ->and($loaded->outcome()->grossResult()->amount())->toBe('900.00000000')
        ->and($loaded->outcome()->netResult()->amount())->toBe('850.00000000')
        ->and($loaded->outcome()->resultType())->toBe(ResultType::GAIN)
        ->and($loaded->outcome()->realizedRR())->toBe('2.50000000')
        ->and($loaded->journal()->rationale()->followedPlan())->toBeTrue()
        ->and($loaded->journal()->emotionalState())->toBe(EmotionalState::CONTROLLED)
        ->and($loaded->lesson()->keepDoing())->toBe('Seguir o plano')
        ->and($loaded->lesson()->improveNextTime())->toBe('Melhorar timing');
});

it('lança exceção quando registro não encontrado', function () {
    $repo = new TradeJournalRepositoryEloquent;
    $repo->getById('non-existent');
})->throws(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
