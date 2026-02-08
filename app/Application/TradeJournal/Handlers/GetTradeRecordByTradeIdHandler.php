<?php

declare(strict_types=1);

namespace App\Application\TradeJournal\Handlers;

use App\Application\Contracts\TradeJournalRepository;
use App\Application\TradeJournal\DTOs\TradeRecordViewDTO;
use App\Application\TradeJournal\Queries\GetTradeRecordByTradeIdQuery;
use App\Domain\Journal\Aggregates\TradeRecord;

final class GetTradeRecordByTradeIdHandler
{
    public function __construct(
        private readonly TradeJournalRepository $journalRepository,
    ) {}

    public function handle(GetTradeRecordByTradeIdQuery $query): TradeRecordViewDTO
    {
        $record = $this->journalRepository->getByTradeId($query->tradeId);

        return $this->toDTO($record);
    }

    private function toDTO(TradeRecord $record): TradeRecordViewDTO
    {
        $journal = $record->journal();

        return new TradeRecordViewDTO(
            id: $record->id(),
            tradeId: $journal->tradeId(),
            assetSymbol: $journal->assetSymbol(),
            entryPrice: $journal->entryPrice()->amount(),
            currency: $journal->entryPrice()->currency(),
            quantity: $journal->quantity()->value(),
            periodStart: $journal->tradePeriod()->start()->format('c'),
            periodEnd: $journal->tradePeriod()->end()->format('c'),
            isReviewed: $record->isReviewed(),
            hasAttentionFlag: $record->hasAttentionFlag(),
            hasSetupInvalidation: $record->hasSetupInvalidation(),
            resultType: $record->outcome()?->resultType()->value,
            grossResult: $record->outcome()?->grossResult()->amount(),
            netResult: $record->outcome()?->netResult()->amount(),
            realizedRR: $record->outcome()?->realizedRR(),
            followedPlan: $journal->rationale()?->followedPlan(),
            emotionalState: $journal->emotionalState()?->value,
            keepDoing: $record->lesson()?->keepDoing(),
            improveNextTime: $record->lesson()?->improveNextTime(),
            createdAt: $journal->createdAt()->format('c'),
        );
    }
}
