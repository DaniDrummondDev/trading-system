<?php

declare(strict_types=1);

namespace App\Application\Contracts;

use App\Domain\Trade\Aggregates\TradeAggregate;

interface TradeRepository
{
    public function save(TradeAggregate $trade): void;

    public function getById(string $tradeId): TradeAggregate;

    public function getOpenTrades(): array;
}
