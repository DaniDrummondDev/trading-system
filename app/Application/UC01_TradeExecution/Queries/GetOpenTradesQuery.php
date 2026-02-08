<?php

declare(strict_types=1);

namespace App\Application\UC01_TradeExecution\Queries;

final readonly class GetOpenTradesQuery
{
    public function __construct(
        public string $userId,
    ) {}
}
