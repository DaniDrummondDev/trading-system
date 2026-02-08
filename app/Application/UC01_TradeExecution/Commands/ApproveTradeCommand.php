<?php

declare(strict_types=1);

namespace App\Application\UC01_TradeExecution\Commands;

final readonly class ApproveTradeCommand
{
    public function __construct(
        public string $tradeId,
    ) {}
}
