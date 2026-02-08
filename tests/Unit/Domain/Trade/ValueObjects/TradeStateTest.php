<?php

declare(strict_types=1);

use App\Domain\Trade\ValueObjects\TradeState;

// --- Transições válidas ---

it('permite CREATED → ANALYZED', function () {
    $state = TradeState::CREATED->transitionTo(TradeState::ANALYZED);
    expect($state)->toBe(TradeState::ANALYZED);
});

it('permite ANALYZED → RISK_VALIDATED', function () {
    $state = TradeState::ANALYZED->transitionTo(TradeState::RISK_VALIDATED);
    expect($state)->toBe(TradeState::RISK_VALIDATED);
});

it('permite ANALYZED → BLOCKED', function () {
    $state = TradeState::ANALYZED->transitionTo(TradeState::BLOCKED);
    expect($state)->toBe(TradeState::BLOCKED);
});

it('permite RISK_VALIDATED → APPROVED', function () {
    $state = TradeState::RISK_VALIDATED->transitionTo(TradeState::APPROVED);
    expect($state)->toBe(TradeState::APPROVED);
});

it('permite RISK_VALIDATED → BLOCKED', function () {
    $state = TradeState::RISK_VALIDATED->transitionTo(TradeState::BLOCKED);
    expect($state)->toBe(TradeState::BLOCKED);
});

it('permite APPROVED → EXECUTED', function () {
    $state = TradeState::APPROVED->transitionTo(TradeState::EXECUTED);
    expect($state)->toBe(TradeState::EXECUTED);
});

it('permite APPROVED → EXPIRED', function () {
    $state = TradeState::APPROVED->transitionTo(TradeState::EXPIRED);
    expect($state)->toBe(TradeState::EXPIRED);
});

it('permite EXECUTED → CLOSED', function () {
    $state = TradeState::EXECUTED->transitionTo(TradeState::CLOSED);
    expect($state)->toBe(TradeState::CLOSED);
});

// --- Transições proibidas ---

it('bloqueia CREATED → EXECUTED (sem análise)', function () {
    TradeState::CREATED->transitionTo(TradeState::EXECUTED);
})->throws(\DomainException::class);

it('bloqueia CREATED → APPROVED (sem análise e risco)', function () {
    TradeState::CREATED->transitionTo(TradeState::APPROVED);
})->throws(\DomainException::class);

it('bloqueia ANALYZED → EXECUTED (sem aprovação)', function () {
    TradeState::ANALYZED->transitionTo(TradeState::EXECUTED);
})->throws(\DomainException::class);

it('bloqueia EXECUTED → ANALYZED (não pode voltar)', function () {
    TradeState::EXECUTED->transitionTo(TradeState::ANALYZED);
})->throws(\DomainException::class);

// --- Estados terminais ---

it('bloqueia qualquer transição de CLOSED', function () {
    TradeState::CLOSED->transitionTo(TradeState::CREATED);
})->throws(\DomainException::class);

it('bloqueia qualquer transição de BLOCKED', function () {
    TradeState::BLOCKED->transitionTo(TradeState::ANALYZED);
})->throws(\DomainException::class);

it('bloqueia qualquer transição de EXPIRED', function () {
    TradeState::EXPIRED->transitionTo(TradeState::CREATED);
})->throws(\DomainException::class);

// --- isTerminal ---

it('identifica estados terminais', function () {
    expect(TradeState::CLOSED->isTerminal())->toBeTrue()
        ->and(TradeState::BLOCKED->isTerminal())->toBeTrue()
        ->and(TradeState::EXPIRED->isTerminal())->toBeTrue()
        ->and(TradeState::CREATED->isTerminal())->toBeFalse()
        ->and(TradeState::EXECUTED->isTerminal())->toBeFalse();
});

// --- canTransitionTo ---

it('canTransitionTo retorna false para transição inválida', function () {
    expect(TradeState::CREATED->canTransitionTo(TradeState::CLOSED))->toBeFalse();
});

it('canTransitionTo retorna true para transição válida', function () {
    expect(TradeState::CREATED->canTransitionTo(TradeState::ANALYZED))->toBeTrue();
});
