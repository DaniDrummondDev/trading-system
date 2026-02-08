<?php

declare(strict_types=1);

namespace App\Application\UC01_TradeExecution\Queries;

final readonly class GetTradeByIdQuery
{
    public function __construct(
        public string $tradeId,
    ) {}
}
