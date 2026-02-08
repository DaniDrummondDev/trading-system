<?php

declare(strict_types=1);

namespace App\Application\TradeExecution\Queries;

final readonly class GetOpenTradesQuery
{
    public function __construct(
        public string $userId,
    ) {}
}
