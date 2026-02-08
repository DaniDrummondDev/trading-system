<?php

declare(strict_types=1);

namespace App\Domain\Journal\Aggregates;

use App\Domain\Journal\Entities\TradeJournal;
use App\Domain\Journal\Events\TradeRecordCreated;
use App\Domain\Journal\Events\TradeReviewed;
use App\Domain\Journal\ValueObjects\DateRange;
use App\Domain\Journal\ValueObjects\EmotionalState;
use App\Domain\Journal\ValueObjects\Money;
use App\Domain\Journal\ValueObjects\Quantity;
use App\Domain\Journal\ValueObjects\TradeLesson;
use App\Domain\Journal\ValueObjects\TradeOutcome;
use App\Domain\Journal\ValueObjects\TradeRationale;
use App\Domain\Metrics\Events\LearningDataAvailable;
use App\Domain\Shared\AggregateRoot;

final class TradeRecord extends AggregateRoot
{
    private readonly TradeJournal $journal;

    private function __construct(
        string $id,
        TradeJournal $journal,
    ) {
        parent::__construct($id);
        $this->journal = $journal;
    }

    public static function create(
        string $id,
        string $userId,
        string $tradeId,
        string $assetSymbol,
        Money $entryPrice,
        Quantity $quantity,
        DateRange $tradePeriod,
    ): self {
        $journal = new TradeJournal(
            $id,
            $userId,
            $tradeId,
            $assetSymbol,
            $entryPrice,
            $quantity,
            $tradePeriod,
            new \DateTimeImmutable,
        );

        $record = new self($id, $journal);

        $record->recordEvent(new TradeRecordCreated($id, $tradeId, $assetSymbol));

        return $record;
    }

    public static function reconstitute(TradeJournal $journal): self
    {
        return new self($journal->id(), $journal);
    }

    public function userId(): string
    {
        return $this->journal->userId();
    }

    public function review(
        TradeOutcome $outcome,
        TradeRationale $rationale,
        EmotionalState $emotionalState,
        TradeLesson $lesson,
    ): void {
        if ($this->journal->isReviewed()) {
            throw new \DomainException('Trade record already reviewed.');
        }

        $this->journal->review($outcome, $rationale, $emotionalState, $lesson);

        $this->recordEvent(new TradeReviewed(
            $this->id(),
            $this->journal->tradeId(),
            $outcome->resultType()->value,
            $rationale->followedPlan(),
            $emotionalState->value,
        ));

        $this->recordEvent(new LearningDataAvailable($this->journal->tradeId()));
    }

    /**
     * Flag de atenção: emoção divergente + loss.
     * Indica padrão comportamental que merece análise.
     */
    public function hasAttentionFlag(): bool
    {
        if (! $this->journal->isReviewed()) {
            return false;
        }

        $emotionalState = $this->journal->emotionalState();
        $outcome = $this->journal->outcome();

        return $emotionalState !== null
            && $emotionalState->isDivergent()
            && $outcome !== null
            && $outcome->isLoss();
    }

    /**
     * Flag quando execução não seguiu o plano.
     * Não invalida o trade, mas invalida o setup.
     */
    public function hasSetupInvalidation(): bool
    {
        $rationale = $this->journal->rationale();

        return $rationale !== null && ! $rationale->followedPlan();
    }

    public function journal(): TradeJournal
    {
        return $this->journal;
    }

    public function outcome(): ?TradeOutcome
    {
        return $this->journal->outcome();
    }

    public function lesson(): ?TradeLesson
    {
        return $this->journal->lesson();
    }

    public function isReviewed(): bool
    {
        return $this->journal->isReviewed();
    }
}
