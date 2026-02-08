<?php

declare(strict_types=1);

namespace App\Application\TradeJournal\Queries;

final readonly class GetTradeRecordByIdQuery
{
    public function __construct(
        public string $recordId,
    ) {}
}
