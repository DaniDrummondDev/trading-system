<?php

declare(strict_types=1);

namespace App\Application\UC01_TradeExecution\Commands;

final readonly class CreateTradeCommand
{
    public function __construct(
        public string $userId,
        public string $assetSymbol,
        public string $market,
        public string $direction,
        public string $timeframe,
    ) {}
}
