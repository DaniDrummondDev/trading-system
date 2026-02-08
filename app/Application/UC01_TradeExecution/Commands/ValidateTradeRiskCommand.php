<?php

declare(strict_types=1);

namespace App\Application\UC01_TradeExecution\Commands;

final readonly class ValidateTradeRiskCommand
{
    public function __construct(
        public string $tradeId,
        public string $riskPercentage,
        public int $positionSize,
    ) {}
}
