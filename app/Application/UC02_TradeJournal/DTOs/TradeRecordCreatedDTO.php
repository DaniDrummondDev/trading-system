<?php

declare(strict_types=1);

namespace App\Application\UC02_TradeJournal\DTOs;

final readonly class TradeRecordCreatedDTO
{
    public function __construct(
        public string $tradeRecordId,
    ) {}
}
