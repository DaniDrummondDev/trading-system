<?php

declare(strict_types=1);

use App\Domain\Journal\ValueObjects\TradeRationale;

it('cria rationale quando seguiu o plano', function () {
    $rationale = new TradeRationale(followedPlan: true);

    expect($rationale->followedPlan())->toBeTrue()
        ->and($rationale->deviationReason())->toBeNull();
});

it('cria rationale com desvio e razão', function () {
    $rationale = new TradeRationale(
        followedPlan: false,
        deviationReason: 'Entrou antes do sinal de confirmação',
    );

    expect($rationale->followedPlan())->toBeFalse()
        ->and($rationale->deviationReason())->toBe('Entrou antes do sinal de confirmação');
});

it('exige razão quando não seguiu o plano', function () {
    new TradeRationale(followedPlan: false);
})->throws(\InvalidArgumentException::class, 'Deviation reason is required');

it('exige razão não vazia quando não seguiu o plano', function () {
    new TradeRationale(followedPlan: false, deviationReason: '  ');
})->throws(\InvalidArgumentException::class, 'Deviation reason is required');

it('permite razão quando seguiu o plano', function () {
    $rationale = new TradeRationale(followedPlan: true, deviationReason: 'Observação adicional');

    expect($rationale->followedPlan())->toBeTrue()
        ->and($rationale->deviationReason())->toBe('Observação adicional');
});
