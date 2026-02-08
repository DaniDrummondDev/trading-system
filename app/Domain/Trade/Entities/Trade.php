<?php

declare(strict_types=1);

namespace App\Domain\Trade\Entities;

use App\Domain\Shared\Entity;
use App\Domain\Trade\ValueObjects\Asset;
use App\Domain\Trade\ValueObjects\Price;
use App\Domain\Trade\ValueObjects\PriceLevel;
use App\Domain\Trade\ValueObjects\Timeframe;
use App\Domain\Trade\ValueObjects\TradeDirection;
use App\Domain\Trade\ValueObjects\TradeState;

final class Trade extends Entity
{
    private TradeState $state;

    private ?PriceLevel $entry = null;

    private ?PriceLevel $stop = null;

    private ?PriceLevel $target = null;

    private ?string $riskPercentage = null;

    private ?int $positionSize = null;

    private ?Price $executedPrice = null;

    private ?int $executedQuantity = null;

    private ?\DateTimeImmutable $executedAt = null;

    private ?\DateTimeImmutable $closedAt = null;

    private ?string $result = null;

    public function __construct(
        string $id,
        private readonly Asset $asset,
        private readonly TradeDirection $direction,
        private readonly Timeframe $timeframe,
        private readonly \DateTimeImmutable $createdAt,
    ) {
        parent::__construct($id);
        $this->state = TradeState::CREATED;
    }

    public function state(): TradeState
    {
        return $this->state;
    }

    public function asset(): Asset
    {
        return $this->asset;
    }

    public function direction(): TradeDirection
    {
        return $this->direction;
    }

    public function timeframe(): Timeframe
    {
        return $this->timeframe;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function entry(): ?PriceLevel
    {
        return $this->entry;
    }

    public function stop(): ?PriceLevel
    {
        return $this->stop;
    }

    public function target(): ?PriceLevel
    {
        return $this->target;
    }

    public function riskPercentage(): ?string
    {
        return $this->riskPercentage;
    }

    public function positionSize(): ?int
    {
        return $this->positionSize;
    }

    public function executedPrice(): ?Price
    {
        return $this->executedPrice;
    }

    public function executedQuantity(): ?int
    {
        return $this->executedQuantity;
    }

    public function executedAt(): ?\DateTimeImmutable
    {
        return $this->executedAt;
    }

    public function closedAt(): ?\DateTimeImmutable
    {
        return $this->closedAt;
    }

    public function result(): ?string
    {
        return $this->result;
    }

    public function transitionTo(TradeState $newState): void
    {
        $this->state = $this->state->transitionTo($newState);
    }

    public function setAnalysis(PriceLevel $entry, PriceLevel $stop, PriceLevel $target): void
    {
        $this->entry = $entry;
        $this->stop = $stop;
        $this->target = $target;
    }

    public function setRiskValidation(string $riskPercentage, int $positionSize): void
    {
        $this->riskPercentage = $riskPercentage;
        $this->positionSize = $positionSize;
    }

    public function setExecution(Price $price, int $quantity): void
    {
        $this->executedPrice = $price;
        $this->executedQuantity = $quantity;
        $this->executedAt = new \DateTimeImmutable;
    }

    public function setClosed(string $result): void
    {
        $this->result = $result;
        $this->closedAt = new \DateTimeImmutable;
    }
}
