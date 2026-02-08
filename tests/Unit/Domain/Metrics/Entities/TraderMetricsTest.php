<?php

declare(strict_types=1);

use App\Domain\Metrics\Entities\TraderMetrics;
use App\Domain\Metrics\ValueObjects\EmotionalStabilityIndex;
use App\Domain\Metrics\ValueObjects\Expectancy;
use App\Domain\Metrics\ValueObjects\MaxDrawdown;
use App\Domain\Metrics\ValueObjects\PlanDisciplineScore;
use App\Domain\Metrics\ValueObjects\ProfitFactor;
use App\Domain\Metrics\ValueObjects\WinRate;

function createMetrics(): TraderMetrics
{
    return new TraderMetrics(
        'metrics-001',
        'user-001',
        '2025-01',
        new WinRate('0.60'),
        new Expectancy('1.20'),
        new ProfitFactor('1.80'),
        new MaxDrawdown('0.10'),
        new PlanDisciplineScore('0.85'),
        new EmotionalStabilityIndex('0.90'),
        new \DateTimeImmutable('2025-01-31'),
    );
}

it('cria snapshot de métricas', function () {
    $metrics = createMetrics();

    expect($metrics->userId())->toBe('user-001')
        ->and($metrics->period())->toBe('2025-01')
        ->and($metrics->winRate()->value())->toBe('0.60')
        ->and($metrics->expectancy()->value())->toBe('1.20')
        ->and($metrics->profitFactor()->value())->toBe('1.80')
        ->and($metrics->maxDrawdown()->value())->toBe('0.10')
        ->and($metrics->planDisciplineScore()->value())->toBe('0.85')
        ->and($metrics->emotionalStabilityIndex()->value())->toBe('0.90');
});

it('atualiza métricas de performance', function () {
    $metrics = createMetrics();

    $metrics->updatePerformance(
        new WinRate('0.70'),
        new Expectancy('1.50'),
        new ProfitFactor('2.10'),
        new MaxDrawdown('0.08'),
    );

    expect($metrics->winRate()->value())->toBe('0.70')
        ->and($metrics->expectancy()->value())->toBe('1.50')
        ->and($metrics->profitFactor()->value())->toBe('2.10')
        ->and($metrics->maxDrawdown()->value())->toBe('0.08');
});

it('atualiza métricas comportamentais', function () {
    $metrics = createMetrics();

    $metrics->updateBehavioral(
        new PlanDisciplineScore('0.95'),
        new EmotionalStabilityIndex('0.80'),
    );

    expect($metrics->planDisciplineScore()->value())->toBe('0.95')
        ->and($metrics->emotionalStabilityIndex()->value())->toBe('0.80');
});

it('mantém identidade da entity', function () {
    $metrics = createMetrics();

    expect($metrics->id())->toBe('metrics-001');
});
