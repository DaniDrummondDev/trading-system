<?php

declare(strict_types=1);

namespace App\Domain\Metrics\Entities;

use App\Domain\Metrics\ValueObjects\EmotionalStabilityIndex;
use App\Domain\Metrics\ValueObjects\Expectancy;
use App\Domain\Metrics\ValueObjects\MaxDrawdown;
use App\Domain\Metrics\ValueObjects\PlanDisciplineScore;
use App\Domain\Metrics\ValueObjects\ProfitFactor;
use App\Domain\Metrics\ValueObjects\WinRate;
use App\Domain\Shared\Entity;

final class TraderMetrics extends Entity
{
    public function __construct(
        string $id,
        private readonly string $userId,
        private readonly string $period,
        private WinRate $winRate,
        private Expectancy $expectancy,
        private ProfitFactor $profitFactor,
        private MaxDrawdown $maxDrawdown,
        private PlanDisciplineScore $planDisciplineScore,
        private EmotionalStabilityIndex $emotionalStabilityIndex,
        private readonly \DateTimeImmutable $calculatedAt,
    ) {
        parent::__construct($id);
    }

    public function userId(): string
    {
        return $this->userId;
    }

    public function period(): string
    {
        return $this->period;
    }

    public function winRate(): WinRate
    {
        return $this->winRate;
    }

    public function expectancy(): Expectancy
    {
        return $this->expectancy;
    }

    public function profitFactor(): ProfitFactor
    {
        return $this->profitFactor;
    }

    public function maxDrawdown(): MaxDrawdown
    {
        return $this->maxDrawdown;
    }

    public function planDisciplineScore(): PlanDisciplineScore
    {
        return $this->planDisciplineScore;
    }

    public function emotionalStabilityIndex(): EmotionalStabilityIndex
    {
        return $this->emotionalStabilityIndex;
    }

    public function calculatedAt(): \DateTimeImmutable
    {
        return $this->calculatedAt;
    }

    public function updatePerformance(
        WinRate $winRate,
        Expectancy $expectancy,
        ProfitFactor $profitFactor,
        MaxDrawdown $maxDrawdown,
    ): void {
        $this->winRate = $winRate;
        $this->expectancy = $expectancy;
        $this->profitFactor = $profitFactor;
        $this->maxDrawdown = $maxDrawdown;
    }

    public function updateBehavioral(
        PlanDisciplineScore $planDisciplineScore,
        EmotionalStabilityIndex $emotionalStabilityIndex,
    ): void {
        $this->planDisciplineScore = $planDisciplineScore;
        $this->emotionalStabilityIndex = $emotionalStabilityIndex;
    }
}
