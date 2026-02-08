<?php

declare(strict_types=1);

namespace App\Application\Contracts;

use App\Domain\Journal\Entities\TradeJournal;

interface TradeJournalRepository
{
    public function save(TradeJournal $journal): void;

    public function getByTradeId(string $tradeId): TradeJournal;
}
