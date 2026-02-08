<?php

declare(strict_types=1);

namespace App\Application\TradeExecution\Commands;

final readonly class ExpireTradeCommand
{
    public function __construct(
        public string $tradeId,
        public string $reason,
    ) {}
}
