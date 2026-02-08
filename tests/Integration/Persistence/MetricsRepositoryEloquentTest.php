<?php

declare(strict_types=1);

use App\Domain\Metrics\Entities\TraderMetrics;
use App\Domain\Metrics\ValueObjects\EmotionalStabilityIndex;
use App\Domain\Metrics\ValueObjects\Expectancy;
use App\Domain\Metrics\ValueObjects\MaxDrawdown;
use App\Domain\Metrics\ValueObjects\PlanDisciplineScore;
use App\Domain\Metrics\ValueObjects\ProfitFactor;
use App\Domain\Metrics\ValueObjects\WinRate;
use App\Infrastructure\Persistence\Repositories\MetricsRepositoryEloquent;

function createMetricsForPersistence(
    string $id = 'metrics-001',
    string $userId = 'user-001',
    string $period = 'monthly',
): TraderMetrics {
    return new TraderMetrics(
        $id,
        $userId,
        $period,
        new WinRate('0.65'),
        new Expectancy('1.20'),
        new ProfitFactor('2.10'),
        new MaxDrawdown('0.05'),
        new PlanDisciplineScore('0.85'),
        new EmotionalStabilityIndex('0.78'),
        new \DateTimeImmutable('2026-01-31'),
    );
}

it('salva e recupera métricas atuais', function () {
    $repo = new MetricsRepositoryEloquent;
    $metrics = createMetricsForPersistence();

    $repo->update($metrics);
    $loaded = $repo->getCurrent('user-001');

    expect($loaded)->not->toBeNull()
        ->and($loaded->userId())->toBe('user-001')
        ->and($loaded->period())->toBe('monthly')
        ->and($loaded->winRate()->value())->toBe('0.65000000')
        ->and($loaded->expectancy()->value())->toBe('1.20000000')
        ->and($loaded->profitFactor()->value())->toBe('2.10000000')
        ->and($loaded->maxDrawdown()->value())->toBe('0.05000000')
        ->and($loaded->planDisciplineScore()->value())->toBe('0.85000000')
        ->and($loaded->emotionalStabilityIndex()->value())->toBe('0.78000000');
});

it('retorna null quando sem métricas', function () {
    $repo = new MetricsRepositoryEloquent;

    $loaded = $repo->getCurrent('user-999');

    expect($loaded)->toBeNull();
});

it('atualiza métricas existentes (upsert)', function () {
    $repo = new MetricsRepositoryEloquent;

    $metrics = createMetricsForPersistence('metrics-010', 'user-010');
    $repo->update($metrics);

    $updated = new TraderMetrics(
        'metrics-010',
        'user-010',
        'monthly',
        new WinRate('0.72'),
        new Expectancy('1.50'),
        new ProfitFactor('2.50'),
        new MaxDrawdown('0.03'),
        new PlanDisciplineScore('0.90'),
        new EmotionalStabilityIndex('0.85'),
        new \DateTimeImmutable('2026-02-28'),
    );
    $repo->update($updated);

    $loaded = $repo->getCurrent('user-010');
    expect($loaded->winRate()->value())->toBe('0.72000000')
        ->and($loaded->expectancy()->value())->toBe('1.50000000');
});

it('separa métricas por período', function () {
    $repo = new MetricsRepositoryEloquent;

    $monthly = createMetricsForPersistence('metrics-020', 'user-020', 'monthly');
    $weekly = createMetricsForPersistence('metrics-021', 'user-020', 'weekly');

    $repo->update($monthly);
    $repo->update($weekly);

    $loadedMonthly = $repo->getCurrent('user-020', 'monthly');
    $loadedWeekly = $repo->getCurrent('user-020', 'weekly');

    expect($loadedMonthly)->not->toBeNull()
        ->and($loadedMonthly->id())->toBe('metrics-020')
        ->and($loadedWeekly)->not->toBeNull()
        ->and($loadedWeekly->id())->toBe('metrics-021');
});
