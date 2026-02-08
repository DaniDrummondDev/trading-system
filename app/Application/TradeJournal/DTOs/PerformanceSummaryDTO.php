<?php

declare(strict_types=1);

namespace App\Application\TradeJournal\DTOs;

final readonly class PerformanceSummaryDTO
{
    public function __construct(
        public string $userId,
        public string $period,
        public string $winRate,
        public string $expectancy,
        public string $profitFactor,
        public string $maxDrawdown,
        public string $planDisciplineScore,
        public string $emotionalStabilityIndex,
        public string $calculatedAt,
    ) {}
}
