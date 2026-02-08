<?php

declare(strict_types=1);

namespace App\Application\TradeJournal\Queries;

final readonly class GetPerformanceSummaryQuery
{
    public function __construct(
        public string $userId,
        public string $period = 'monthly',
    ) {}
}
