<?php

declare(strict_types=1);

namespace App\Domain\Journal\Entities;

use App\Domain\Journal\ValueObjects\DateRange;
use App\Domain\Journal\ValueObjects\EmotionalState;
use App\Domain\Journal\ValueObjects\Money;
use App\Domain\Journal\ValueObjects\Quantity;
use App\Domain\Journal\ValueObjects\TradeLesson;
use App\Domain\Journal\ValueObjects\TradeOutcome;
use App\Domain\Journal\ValueObjects\TradeRationale;
use App\Domain\Shared\Entity;

final class TradeJournal extends Entity
{
    private ?TradeOutcome $outcome = null;

    private ?TradeRationale $rationale = null;

    private ?EmotionalState $emotionalState = null;

    private ?TradeLesson $lesson = null;

    private bool $reviewed = false;

    public function __construct(
        string $id,
        private readonly string $tradeId,
        private readonly string $assetSymbol,
        private readonly Money $entryPrice,
        private readonly Quantity $quantity,
        private readonly DateRange $tradePeriod,
        private readonly \DateTimeImmutable $createdAt,
    ) {
        parent::__construct($id);
    }

    public function tradeId(): string
    {
        return $this->tradeId;
    }

    public function assetSymbol(): string
    {
        return $this->assetSymbol;
    }

    public function entryPrice(): Money
    {
        return $this->entryPrice;
    }

    public function quantity(): Quantity
    {
        return $this->quantity;
    }

    public function tradePeriod(): DateRange
    {
        return $this->tradePeriod;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function outcome(): ?TradeOutcome
    {
        return $this->outcome;
    }

    public function rationale(): ?TradeRationale
    {
        return $this->rationale;
    }

    public function emotionalState(): ?EmotionalState
    {
        return $this->emotionalState;
    }

    public function lesson(): ?TradeLesson
    {
        return $this->lesson;
    }

    public function isReviewed(): bool
    {
        return $this->reviewed;
    }

    public function review(
        TradeOutcome $outcome,
        TradeRationale $rationale,
        EmotionalState $emotionalState,
        TradeLesson $lesson,
    ): void {
        $this->outcome = $outcome;
        $this->rationale = $rationale;
        $this->emotionalState = $emotionalState;
        $this->lesson = $lesson;
        $this->reviewed = true;
    }
}
