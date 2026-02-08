<?php

declare(strict_types=1);

use App\Domain\Trade\Aggregates\TradeAggregate;
use App\Domain\Trade\Events\TradeAnalyzed;
use App\Domain\Trade\Events\TradeApproved;
use App\Domain\Trade\Events\TradeBlocked;
use App\Domain\Trade\Events\TradeClosed;
use App\Domain\Trade\Events\TradeCreated;
use App\Domain\Trade\Events\TradeExecuted;
use App\Domain\Trade\Events\TradeExpired;
use App\Domain\Trade\Events\TradeRiskValidated;
use App\Domain\Trade\ValueObjects\Asset;
use App\Domain\Trade\ValueObjects\Price;
use App\Domain\Trade\ValueObjects\PriceLevel;
use App\Domain\Trade\ValueObjects\PriceLevelType;
use App\Domain\Trade\ValueObjects\Reason;
use App\Domain\Trade\ValueObjects\Timeframe;
use App\Domain\Trade\ValueObjects\TradeDirection;
use App\Domain\Trade\ValueObjects\TradeState;

function createTradeAggregate(string $id = 'trade-001'): TradeAggregate
{
    return TradeAggregate::create(
        $id,
        'user-001',
        new Asset('PETR4', 'B3'),
        TradeDirection::LONG,
        Timeframe::D1,
    );
}

function makeEntry(): PriceLevel
{
    return new PriceLevel(new Price('25.50'), PriceLevelType::ENTRY);
}

function makeStop(): PriceLevel
{
    return new PriceLevel(new Price('24.00'), PriceLevelType::STOP);
}

function makeTarget(): PriceLevel
{
    return new PriceLevel(new Price('30.00'), PriceLevelType::TARGET);
}

// --- Criação ---

it('cria trade com estado CREATED e evento TradeCreated', function () {
    $trade = createTradeAggregate();

    expect($trade->state())->toBe(TradeState::CREATED)
        ->and($trade->asset()->symbol())->toBe('PETR4')
        ->and($trade->direction())->toBe(TradeDirection::LONG)
        ->and($trade->timeframe())->toBe(Timeframe::D1);

    $events = $trade->releaseEvents();
    expect($events)->toHaveCount(1)
        ->and($events[0])->toBeInstanceOf(TradeCreated::class);
});

// --- Ciclo completo happy path ---

it('completa ciclo completo: create → analyze → risk → approve → execute → close', function () {
    $trade = createTradeAggregate();
    $trade->releaseEvents(); // limpa TradeCreated

    $trade->analyze(makeEntry(), makeStop(), makeTarget());
    expect($trade->state())->toBe(TradeState::ANALYZED);

    $trade->validateRisk('1.5', 200);
    expect($trade->state())->toBe(TradeState::RISK_VALIDATED);

    $trade->approve();
    expect($trade->state())->toBe(TradeState::APPROVED);

    $trade->execute(new Price('25.40'), 200);
    expect($trade->state())->toBe(TradeState::EXECUTED);

    $trade->close('1200.00');
    expect($trade->state())->toBe(TradeState::CLOSED);

    $events = $trade->releaseEvents();
    expect($events)->toHaveCount(5)
        ->and($events[0])->toBeInstanceOf(TradeAnalyzed::class)
        ->and($events[1])->toBeInstanceOf(TradeRiskValidated::class)
        ->and($events[2])->toBeInstanceOf(TradeApproved::class)
        ->and($events[3])->toBeInstanceOf(TradeExecuted::class)
        ->and($events[4])->toBeInstanceOf(TradeClosed::class);
});

// --- Bloqueio ---

it('bloqueia trade após análise', function () {
    $trade = createTradeAggregate();
    $trade->analyze(makeEntry(), makeStop(), makeTarget());
    $trade->releaseEvents();

    $trade->block(
        new Reason('RISK_EXCEEDED', 'Risco máximo por trade excedido'),
        new Reason('MAX_TRADES', 'Máximo de trades simultâneos atingido'),
    );

    expect($trade->state())->toBe(TradeState::BLOCKED);

    $events = $trade->releaseEvents();
    expect($events)->toHaveCount(1)
        ->and($events[0])->toBeInstanceOf(TradeBlocked::class)
        ->and($events[0]->reasons())->toHaveCount(2);
});

it('bloqueia trade após validação de risco', function () {
    $trade = createTradeAggregate();
    $trade->analyze(makeEntry(), makeStop(), makeTarget());
    $trade->validateRisk('1.5', 200);

    $trade->block(new Reason('DRAWDOWN', 'Drawdown máximo atingido'));

    expect($trade->state())->toBe(TradeState::BLOCKED);
});

// --- Expiração ---

it('expira trade aprovado', function () {
    $trade = createTradeAggregate();
    $trade->analyze(makeEntry(), makeStop(), makeTarget());
    $trade->validateRisk('1.5', 200);
    $trade->approve();
    $trade->releaseEvents();

    $trade->expire('Oportunidade expirou sem execução');

    expect($trade->state())->toBe(TradeState::EXPIRED);

    $events = $trade->releaseEvents();
    expect($events)->toHaveCount(1)
        ->and($events[0])->toBeInstanceOf(TradeExpired::class);
});

// --- Invariantes ---

it('não permite executar sem aprovação', function () {
    $trade = createTradeAggregate();
    $trade->analyze(makeEntry(), makeStop(), makeTarget());
    $trade->validateRisk('1.5', 200);

    $trade->execute(new Price('25.40'), 200);
})->throws(\DomainException::class);

it('não permite fechar sem executar', function () {
    $trade = createTradeAggregate();
    $trade->analyze(makeEntry(), makeStop(), makeTarget());
    $trade->validateRisk('1.5', 200);
    $trade->approve();

    $trade->close('1200.00');
})->throws(\DomainException::class);

it('não permite analisar trade bloqueado', function () {
    $trade = createTradeAggregate();
    $trade->analyze(makeEntry(), makeStop(), makeTarget());
    $trade->block(new Reason('RISK', 'Risco'));

    $trade->analyze(makeEntry(), makeStop(), makeTarget());
})->throws(\DomainException::class);

it('não permite transições de trade fechado', function () {
    $trade = createTradeAggregate();
    $trade->analyze(makeEntry(), makeStop(), makeTarget());
    $trade->validateRisk('1.5', 200);
    $trade->approve();
    $trade->execute(new Price('25.40'), 200);
    $trade->close('1200.00');

    $trade->analyze(makeEntry(), makeStop(), makeTarget());
})->throws(\DomainException::class);

// --- Dados da Entity ---

it('armazena dados de análise na entity', function () {
    $trade = createTradeAggregate();
    $entry = makeEntry();
    $stop = makeStop();
    $target = makeTarget();

    $trade->analyze($entry, $stop, $target);

    $entity = $trade->trade();
    expect($entity->entry())->toBe($entry)
        ->and($entity->stop())->toBe($stop)
        ->and($entity->target())->toBe($target);
});

it('armazena dados de execução na entity', function () {
    $trade = createTradeAggregate();
    $trade->analyze(makeEntry(), makeStop(), makeTarget());
    $trade->validateRisk('1.5', 200);
    $trade->approve();
    $trade->execute(new Price('25.40'), 200);

    $entity = $trade->trade();
    expect($entity->executedPrice()->amount())->toBe('25.40')
        ->and($entity->executedQuantity())->toBe(200)
        ->and($entity->executedAt())->toBeInstanceOf(\DateTimeImmutable::class);
});
