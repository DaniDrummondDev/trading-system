<?php

declare(strict_types=1);

use App\Domain\Metrics\ValueObjects\EmotionalStabilityIndex;
use App\Domain\Metrics\ValueObjects\Expectancy;
use App\Domain\Metrics\ValueObjects\KPI;
use App\Domain\Metrics\ValueObjects\MaxDrawdown;
use App\Domain\Metrics\ValueObjects\PlanDisciplineScore;
use App\Domain\Metrics\ValueObjects\ProfitFactor;
use App\Domain\Metrics\ValueObjects\WinRate;

// --- KPI ---

it('cria KPI genérico', function () {
    $kpi = new KPI('Win Rate', '0.65', '2025-01');

    expect($kpi->name())->toBe('Win Rate')
        ->and($kpi->value())->toBe('0.65')
        ->and($kpi->period())->toBe('2025-01');
});

it('rejeita KPI com nome vazio', function () {
    new KPI('', '0.65', '2025-01');
})->throws(\InvalidArgumentException::class);

it('rejeita KPI com valor não numérico', function () {
    new KPI('WR', 'abc', '2025-01');
})->throws(\InvalidArgumentException::class);

// --- WinRate ---

it('cria WinRate válido', function () {
    $wr = new WinRate('0.65');

    expect($wr->value())->toBe('0.65')
        ->and($wr->asPercentage())->toBe('65.00');
});

it('aceita WinRate 0 e 1', function () {
    $zero = new WinRate('0');
    $one = new WinRate('1');

    expect($zero->asPercentage())->toBe('0.00')
        ->and($one->asPercentage())->toBe('100.00');
});

it('rejeita WinRate acima de 1', function () {
    new WinRate('1.1');
})->throws(\InvalidArgumentException::class, 'between 0 and 1');

it('rejeita WinRate negativo', function () {
    new WinRate('-0.1');
})->throws(\InvalidArgumentException::class, 'between 0 and 1');

// --- Expectancy ---

it('cria Expectancy positiva', function () {
    $exp = new Expectancy('1.25');
    expect($exp->isPositive())->toBeTrue();
});

it('cria Expectancy negativa', function () {
    $exp = new Expectancy('-0.50');
    expect($exp->isPositive())->toBeFalse();
});

// --- ProfitFactor ---

it('cria ProfitFactor válido', function () {
    $pf = new ProfitFactor('1.8');
    expect($pf->value())->toBe('1.8');
});

it('rejeita ProfitFactor negativo', function () {
    new ProfitFactor('-0.5');
})->throws(\InvalidArgumentException::class, '>= 0');

// --- MaxDrawdown ---

it('cria MaxDrawdown válido', function () {
    $dd = new MaxDrawdown('0.15');

    expect($dd->value())->toBe('0.15')
        ->and($dd->asPercentage())->toBe('15.00');
});

it('rejeita MaxDrawdown acima de 1', function () {
    new MaxDrawdown('1.5');
})->throws(\InvalidArgumentException::class, 'between 0 and 1');

// --- PlanDisciplineScore ---

it('cria PlanDisciplineScore válido', function () {
    $score = new PlanDisciplineScore('0.85');
    expect($score->asPercentage())->toBe('85.00');
});

it('rejeita PlanDisciplineScore fora do range', function () {
    new PlanDisciplineScore('1.5');
})->throws(\InvalidArgumentException::class, 'between 0 and 1');

// --- EmotionalStabilityIndex ---

it('cria EmotionalStabilityIndex válido', function () {
    $esi = new EmotionalStabilityIndex('0.90');
    expect($esi->asPercentage())->toBe('90.00');
});

it('rejeita EmotionalStabilityIndex negativo', function () {
    new EmotionalStabilityIndex('-0.1');
})->throws(\InvalidArgumentException::class, 'between 0 and 1');

// --- Igualdade ---

it('WinRate equals funciona', function () {
    $a = new WinRate('0.65');
    $b = new WinRate('0.65');
    $c = new WinRate('0.70');

    expect($a->equals($b))->toBeTrue()
        ->and($a->equals($c))->toBeFalse();
});
