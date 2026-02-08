<?php

declare(strict_types=1);

namespace App\Application\UC02_TradeJournal\Queries;

final readonly class GetPerformanceSummaryQuery
{
    public function __construct(
        public string $userId,
        public string $period = 'monthly',
    ) {}
}
