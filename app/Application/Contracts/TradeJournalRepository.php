<?php

declare(strict_types=1);

namespace App\Application\Contracts;

use App\Domain\Journal\Aggregates\TradeRecord;
use App\Domain\Journal\ValueObjects\DateRange;

interface TradeJournalRepository
{
    public function save(TradeRecord $record): void;

    public function getById(string $recordId): TradeRecord;

    public function getByTradeId(string $tradeId): TradeRecord;

    /** @return TradeRecord[] */
    public function getByUserId(string $userId, ?DateRange $period = null): array;
}
