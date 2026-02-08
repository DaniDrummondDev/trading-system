<?php

declare(strict_types=1);

namespace App\Domain\Trade\Aggregates;

use App\Domain\Shared\AggregateRoot;
use App\Domain\Trade\Entities\Trade;
use App\Domain\Trade\Events\TradeAnalyzed;
use App\Domain\Trade\Events\TradeApproved;
use App\Domain\Trade\Events\TradeBlocked;
use App\Domain\Trade\Events\TradeClosed;
use App\Domain\Trade\Events\TradeCreated;
use App\Domain\Trade\Events\TradeExecuted;
use App\Domain\Trade\Events\TradeExpired;
use App\Domain\Trade\Events\TradeRiskValidated;
use App\Domain\Trade\ValueObjects\Asset;
use App\Domain\Trade\ValueObjects\Price;
use App\Domain\Trade\ValueObjects\PriceLevel;
use App\Domain\Trade\ValueObjects\Reason;
use App\Domain\Trade\ValueObjects\Timeframe;
use App\Domain\Trade\ValueObjects\TradeDirection;
use App\Domain\Trade\ValueObjects\TradeState;

final class TradeAggregate extends AggregateRoot
{
    private readonly Trade $trade;

    private function __construct(
        string $id,
        Trade $trade,
    ) {
        parent::__construct($id);
        $this->trade = $trade;
    }

    public static function create(
        string $id,
        string $userId,
        Asset $asset,
        TradeDirection $direction,
        Timeframe $timeframe,
    ): self {
        $trade = new Trade($id, $userId, $asset, $direction, $timeframe, new \DateTimeImmutable);
        $aggregate = new self($id, $trade);

        $aggregate->recordEvent(new TradeCreated(
            $id,
            $asset->symbol(),
            $direction->value,
            $timeframe->value,
        ));

        return $aggregate;
    }

    public static function reconstitute(Trade $trade): self
    {
        return new self($trade->id(), $trade);
    }

    public function analyze(PriceLevel $entry, PriceLevel $stop, PriceLevel $target): void
    {
        $this->trade->transitionTo(TradeState::ANALYZED);
        $this->trade->setAnalysis($entry, $stop, $target);

        $this->recordEvent(new TradeAnalyzed(
            $this->id(),
            $entry->price()->amount(),
            $stop->price()->amount(),
            $target->price()->amount(),
        ));
    }

    public function validateRisk(string $riskPercentage, int $positionSize): void
    {
        $this->trade->transitionTo(TradeState::RISK_VALIDATED);
        $this->trade->setRiskValidation($riskPercentage, $positionSize);

        $this->recordEvent(new TradeRiskValidated(
            $this->id(),
            $riskPercentage,
            $positionSize,
        ));
    }

    public function approve(): void
    {
        $this->trade->transitionTo(TradeState::APPROVED);

        $this->recordEvent(new TradeApproved($this->id()));
    }

    public function block(Reason ...$reasons): void
    {
        $this->trade->transitionTo(TradeState::BLOCKED);

        $this->recordEvent(new TradeBlocked(
            $this->id(),
            array_map(
                fn (Reason $r) => "[{$r->code()}] {$r->description()}",
                $reasons,
            ),
        ));
    }

    public function execute(Price $executedPrice, int $quantity): void
    {
        $this->trade->transitionTo(TradeState::EXECUTED);
        $this->trade->setExecution($executedPrice, $quantity);

        $this->recordEvent(new TradeExecuted(
            $this->id(),
            $executedPrice->amount(),
            $quantity,
        ));
    }

    public function close(string $result): void
    {
        $this->trade->transitionTo(TradeState::CLOSED);
        $this->trade->setClosed($result);

        $this->recordEvent(new TradeClosed($this->id(), $result));
    }

    public function expire(string $reason): void
    {
        $this->trade->transitionTo(TradeState::EXPIRED);

        $this->recordEvent(new TradeExpired($this->id(), $reason));
    }

    public function state(): TradeState
    {
        return $this->trade->state();
    }

    public function userId(): string
    {
        return $this->trade->userId();
    }

    public function asset(): Asset
    {
        return $this->trade->asset();
    }

    public function direction(): TradeDirection
    {
        return $this->trade->direction();
    }

    public function timeframe(): Timeframe
    {
        return $this->trade->timeframe();
    }

    public function trade(): Trade
    {
        return $this->trade;
    }
}
