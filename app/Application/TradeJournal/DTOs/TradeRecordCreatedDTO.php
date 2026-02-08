<?php

declare(strict_types=1);

namespace App\Application\TradeJournal\DTOs;

final readonly class TradeRecordCreatedDTO
{
    public function __construct(
        public string $tradeRecordId,
    ) {}
}
