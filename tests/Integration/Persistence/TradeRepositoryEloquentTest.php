<?php

declare(strict_types=1);

use App\Domain\Trade\Aggregates\TradeAggregate;
use App\Domain\Trade\ValueObjects\Asset;
use App\Domain\Trade\ValueObjects\Price;
use App\Domain\Trade\ValueObjects\PriceLevel;
use App\Domain\Trade\ValueObjects\PriceLevelType;
use App\Domain\Trade\ValueObjects\Timeframe;
use App\Domain\Trade\ValueObjects\TradeDirection;
use App\Domain\Trade\ValueObjects\TradeState;
use App\Infrastructure\Persistence\Repositories\TradeRepositoryEloquent;

function createTradeForPersistence(string $id = 'trade-001', string $userId = 'user-001'): TradeAggregate
{
    $trade = TradeAggregate::create($id, $userId, new Asset('PETR4', 'B3'), TradeDirection::LONG, Timeframe::D1);
    $trade->releaseEvents();

    return $trade;
}

it('salva e recupera trade por ID', function () {
    $repo = new TradeRepositoryEloquent;
    $trade = createTradeForPersistence();

    $repo->save($trade);
    $loaded = $repo->getById('trade-001');

    expect($loaded->id())->toBe('trade-001')
        ->and($loaded->userId())->toBe('user-001')
        ->and($loaded->asset()->symbol())->toBe('PETR4')
        ->and($loaded->asset()->market())->toBe('B3')
        ->and($loaded->direction())->toBe(TradeDirection::LONG)
        ->and($loaded->timeframe())->toBe(Timeframe::D1)
        ->and($loaded->state())->toBe(TradeState::CREATED);
});

it('salva trade com análise completa', function () {
    $repo = new TradeRepositoryEloquent;
    $trade = createTradeForPersistence('trade-002');
    $trade->analyze(
        new PriceLevel(new Price('25.50'), PriceLevelType::ENTRY),
        new PriceLevel(new Price('24.00'), PriceLevelType::STOP),
        new PriceLevel(new Price('30.00'), PriceLevelType::TARGET),
    );
    $trade->releaseEvents();

    $repo->save($trade);
    $loaded = $repo->getById('trade-002');

    expect($loaded->state())->toBe(TradeState::ANALYZED)
        ->and($loaded->trade()->entry()->price()->amount())->toBe('25.50000000')
        ->and($loaded->trade()->stop()->price()->amount())->toBe('24.00000000')
        ->and($loaded->trade()->target()->price()->amount())->toBe('30.00000000');
});

it('salva trade com risco validado', function () {
    $repo = new TradeRepositoryEloquent;
    $trade = createTradeForPersistence('trade-003');
    $trade->analyze(
        new PriceLevel(new Price('25.50'), PriceLevelType::ENTRY),
        new PriceLevel(new Price('24.00'), PriceLevelType::STOP),
        new PriceLevel(new Price('30.00'), PriceLevelType::TARGET),
    );
    $trade->validateRisk('1.5', 200);
    $trade->releaseEvents();

    $repo->save($trade);
    $loaded = $repo->getById('trade-003');

    expect($loaded->state())->toBe(TradeState::RISK_VALIDATED)
        ->and($loaded->trade()->riskPercentage())->toBe('1.50000000')
        ->and($loaded->trade()->positionSize())->toBe(200);
});

it('salva trade executado', function () {
    $repo = new TradeRepositoryEloquent;
    $trade = createTradeForPersistence('trade-004');
    $trade->analyze(
        new PriceLevel(new Price('25.50'), PriceLevelType::ENTRY),
        new PriceLevel(new Price('24.00'), PriceLevelType::STOP),
        new PriceLevel(new Price('30.00'), PriceLevelType::TARGET),
    );
    $trade->validateRisk('1.5', 200);
    $trade->approve();
    $trade->execute(new Price('25.40'), 200);
    $trade->releaseEvents();

    $repo->save($trade);
    $loaded = $repo->getById('trade-004');

    expect($loaded->state())->toBe(TradeState::EXECUTED)
        ->and($loaded->trade()->executedPrice()->amount())->toBe('25.40000000')
        ->and($loaded->trade()->executedQuantity())->toBe(200)
        ->and($loaded->trade()->executedAt())->toBeInstanceOf(\DateTimeImmutable::class);
});

it('salva trade fechado', function () {
    $repo = new TradeRepositoryEloquent;
    $trade = createTradeForPersistence('trade-005');
    $trade->analyze(
        new PriceLevel(new Price('25.50'), PriceLevelType::ENTRY),
        new PriceLevel(new Price('24.00'), PriceLevelType::STOP),
        new PriceLevel(new Price('30.00'), PriceLevelType::TARGET),
    );
    $trade->validateRisk('1.5', 200);
    $trade->approve();
    $trade->execute(new Price('25.40'), 200);
    $trade->close('GAIN');
    $trade->releaseEvents();

    $repo->save($trade);
    $loaded = $repo->getById('trade-005');

    expect($loaded->state())->toBe(TradeState::CLOSED)
        ->and($loaded->trade()->result())->toBe('GAIN')
        ->and($loaded->trade()->closedAt())->toBeInstanceOf(\DateTimeImmutable::class);
});

it('retorna trades abertos do usuário', function () {
    $repo = new TradeRepositoryEloquent;

    $trade1 = createTradeForPersistence('trade-010', 'user-010');
    $trade1->releaseEvents();
    $repo->save($trade1);

    $trade2 = createTradeForPersistence('trade-011', 'user-010');
    $trade2->analyze(
        new PriceLevel(new Price('25.50'), PriceLevelType::ENTRY),
        new PriceLevel(new Price('24.00'), PriceLevelType::STOP),
        new PriceLevel(new Price('30.00'), PriceLevelType::TARGET),
    );
    $trade2->validateRisk('1.5', 200);
    $trade2->approve();
    $trade2->execute(new Price('25.40'), 200);
    $trade2->close('GAIN');
    $trade2->releaseEvents();
    $repo->save($trade2);

    $open = $repo->getOpenTrades('user-010');

    expect($open)->toHaveCount(1)
        ->and($open[0]->id())->toBe('trade-010');
});

it('não retorna trades de outro usuário', function () {
    $repo = new TradeRepositoryEloquent;

    $trade = createTradeForPersistence('trade-020', 'user-020');
    $trade->releaseEvents();
    $repo->save($trade);

    $open = $repo->getOpenTrades('user-999');

    expect($open)->toBeEmpty();
});

it('lança exceção quando trade não encontrado', function () {
    $repo = new TradeRepositoryEloquent;
    $repo->getById('non-existent');
})->throws(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

it('atualiza trade existente (upsert)', function () {
    $repo = new TradeRepositoryEloquent;

    $trade = createTradeForPersistence('trade-030');
    $repo->save($trade);

    $trade->analyze(
        new PriceLevel(new Price('25.50'), PriceLevelType::ENTRY),
        new PriceLevel(new Price('24.00'), PriceLevelType::STOP),
        new PriceLevel(new Price('30.00'), PriceLevelType::TARGET),
    );
    $trade->releaseEvents();
    $repo->save($trade);

    $loaded = $repo->getById('trade-030');
    expect($loaded->state())->toBe(TradeState::ANALYZED);
});
