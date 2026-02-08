<?php

declare(strict_types=1);

namespace App\Application\TradeJournal\Commands;

final readonly class RegisterTradeExecutionCommand
{
    public function __construct(
        public string $tradeId,
        public string $entryPrice,
        public string $currency,
        public int $quantity,
        public string $entryDate,
        public string $exitDate,
    ) {}
}
