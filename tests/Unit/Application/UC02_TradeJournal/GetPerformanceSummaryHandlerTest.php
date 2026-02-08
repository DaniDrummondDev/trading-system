<?php

declare(strict_types=1);

use App\Application\Contracts\MetricsRepository;
use App\Application\UC02_TradeJournal\DTOs\PerformanceSummaryDTO;
use App\Application\UC02_TradeJournal\Handlers\GetPerformanceSummaryHandler;
use App\Application\UC02_TradeJournal\Queries\GetPerformanceSummaryQuery;
use App\Domain\Metrics\Entities\TraderMetrics;
use App\Domain\Metrics\ValueObjects\EmotionalStabilityIndex;
use App\Domain\Metrics\ValueObjects\Expectancy;
use App\Domain\Metrics\ValueObjects\MaxDrawdown;
use App\Domain\Metrics\ValueObjects\PlanDisciplineScore;
use App\Domain\Metrics\ValueObjects\ProfitFactor;
use App\Domain\Metrics\ValueObjects\WinRate;

it('retorna resumo de performance com métricas existentes', function () {
    $metrics = new TraderMetrics(
        'metrics-001',
        'user-001',
        'monthly',
        new WinRate('0.60'),
        new Expectancy('1.20'),
        new ProfitFactor('1.80'),
        new MaxDrawdown('0.10'),
        new PlanDisciplineScore('0.85'),
        new EmotionalStabilityIndex('0.90'),
        new \DateTimeImmutable('2026-01-31'),
    );

    $metricsRepo = Mockery::mock(MetricsRepository::class);
    $metricsRepo->shouldReceive('getCurrent')
        ->with('user-001', 'monthly')
        ->andReturn($metrics);

    $handler = new GetPerformanceSummaryHandler($metricsRepo);
    $result = $handler->handle(new GetPerformanceSummaryQuery(
        userId: 'user-001',
        period: 'monthly',
    ));

    expect($result)->toBeInstanceOf(PerformanceSummaryDTO::class)
        ->and($result->userId)->toBe('user-001')
        ->and($result->winRate)->toBe('0.60')
        ->and($result->expectancy)->toBe('1.20')
        ->and($result->profitFactor)->toBe('1.80')
        ->and($result->maxDrawdown)->toBe('0.10');
});

it('retorna valores zerados quando não há métricas', function () {
    $metricsRepo = Mockery::mock(MetricsRepository::class);
    $metricsRepo->shouldReceive('getCurrent')
        ->with('user-new', 'monthly')
        ->andReturn(null);

    $handler = new GetPerformanceSummaryHandler($metricsRepo);
    $result = $handler->handle(new GetPerformanceSummaryQuery(
        userId: 'user-new',
        period: 'monthly',
    ));

    expect($result)->toBeInstanceOf(PerformanceSummaryDTO::class)
        ->and($result->userId)->toBe('user-new')
        ->and($result->winRate)->toBe('0')
        ->and($result->expectancy)->toBe('0')
        ->and($result->calculatedAt)->toBe('');
});
