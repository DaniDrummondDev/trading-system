<?php

declare(strict_types=1);

namespace App\Domain\Journal\ValueObjects;

use App\Domain\Shared\ValueObject;

final class TradeRationale extends ValueObject
{
    public function __construct(
        private readonly bool $followedPlan,
        private readonly ?string $deviationReason = null,
    ) {
        if (! $this->followedPlan && ($this->deviationReason === null || trim($this->deviationReason) === '')) {
            throw new \InvalidArgumentException(
                'Deviation reason is required when trade did not follow the plan.'
            );
        }
    }

    public function followedPlan(): bool
    {
        return $this->followedPlan;
    }

    public function deviationReason(): ?string
    {
        return $this->deviationReason;
    }

    public function equals(ValueObject $other): bool
    {
        return $other instanceof self
            && $this->followedPlan === $other->followedPlan
            && $this->deviationReason === $other->deviationReason;
    }
}
