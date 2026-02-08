<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Application\Contracts\MetricsRepository;
use App\Domain\Metrics\Entities\TraderMetrics;
use App\Domain\Metrics\ValueObjects\EmotionalStabilityIndex;
use App\Domain\Metrics\ValueObjects\Expectancy;
use App\Domain\Metrics\ValueObjects\MaxDrawdown;
use App\Domain\Metrics\ValueObjects\PlanDisciplineScore;
use App\Domain\Metrics\ValueObjects\ProfitFactor;
use App\Domain\Metrics\ValueObjects\WinRate;
use App\Infrastructure\Persistence\Eloquent\TraderMetricsModel;

final class MetricsRepositoryEloquent implements MetricsRepository
{
    public function update(TraderMetrics $metrics): void
    {
        TraderMetricsModel::updateOrCreate(
            [
                'user_id' => $metrics->userId(),
                'period' => $metrics->period(),
            ],
            [
                'id' => $metrics->id(),
                'win_rate' => $metrics->winRate()->value(),
                'expectancy' => $metrics->expectancy()->value(),
                'profit_factor' => $metrics->profitFactor()->value(),
                'max_drawdown' => $metrics->maxDrawdown()->value(),
                'plan_discipline_score' => $metrics->planDisciplineScore()->value(),
                'emotional_stability_index' => $metrics->emotionalStabilityIndex()->value(),
                'calculated_at' => $metrics->calculatedAt(),
            ],
        );
    }

    public function getCurrent(string $userId, string $period = 'monthly'): ?TraderMetrics
    {
        $model = TraderMetricsModel::where('user_id', $userId)
            ->where('period', $period)
            ->first();

        if ($model === null) {
            return null;
        }

        return $this->hydrate($model);
    }

    private function hydrate(TraderMetricsModel $model): TraderMetrics
    {
        /** @var \DateTimeImmutable $calculatedAt */
        $calculatedAt = $model->calculated_at;

        return new TraderMetrics(
            id: $model->id,
            userId: $model->user_id,
            period: $model->period,
            winRate: new WinRate((string) $model->win_rate),
            expectancy: new Expectancy((string) $model->expectancy),
            profitFactor: new ProfitFactor((string) $model->profit_factor),
            maxDrawdown: new MaxDrawdown((string) $model->max_drawdown),
            planDisciplineScore: new PlanDisciplineScore((string) $model->plan_discipline_score),
            emotionalStabilityIndex: new EmotionalStabilityIndex((string) $model->emotional_stability_index),
            calculatedAt: $calculatedAt,
        );
    }
}
