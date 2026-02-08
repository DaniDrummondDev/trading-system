<?php

declare(strict_types=1);

namespace App\Application\UC02_TradeJournal\Handlers;

use App\Application\Contracts\MetricsRepository;
use App\Application\UC02_TradeJournal\DTOs\PerformanceSummaryDTO;
use App\Application\UC02_TradeJournal\Queries\GetPerformanceSummaryQuery;

final class GetPerformanceSummaryHandler
{
    public function __construct(
        private readonly MetricsRepository $metricsRepository,
    ) {}

    public function handle(GetPerformanceSummaryQuery $query): PerformanceSummaryDTO
    {
        $metrics = $this->metricsRepository->getCurrent($query->userId, $query->period);

        if ($metrics === null) {
            return new PerformanceSummaryDTO(
                userId: $query->userId,
                period: $query->period,
                winRate: '0',
                expectancy: '0',
                profitFactor: '0',
                maxDrawdown: '0',
                planDisciplineScore: '0',
                emotionalStabilityIndex: '0',
                calculatedAt: '',
            );
        }

        return new PerformanceSummaryDTO(
            userId: $metrics->userId(),
            period: $metrics->period(),
            winRate: $metrics->winRate()->value(),
            expectancy: $metrics->expectancy()->value(),
            profitFactor: $metrics->profitFactor()->value(),
            maxDrawdown: $metrics->maxDrawdown()->value(),
            planDisciplineScore: $metrics->planDisciplineScore()->value(),
            emotionalStabilityIndex: $metrics->emotionalStabilityIndex()->value(),
            calculatedAt: $metrics->calculatedAt()->format('c'),
        );
    }
}
