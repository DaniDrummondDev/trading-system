<?php

declare(strict_types=1);

namespace App\Application\UC01_TradeExecution\Commands;

final readonly class AnalyzeTradeCommand
{
    public function __construct(
        public string $tradeId,
        public string $entryPrice,
        public string $stopPrice,
        public string $targetPrice,
    ) {}
}
