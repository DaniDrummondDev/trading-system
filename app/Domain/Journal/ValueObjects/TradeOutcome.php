<?php

declare(strict_types=1);

namespace App\Domain\Journal\ValueObjects;

use App\Domain\Shared\ValueObject;

final class TradeOutcome extends ValueObject
{
    public function __construct(
        private readonly Money $grossResult,
        private readonly Money $netResult,
        private readonly ResultType $resultType,
        private readonly string $realizedRR,
    ) {
        if (! is_numeric($this->realizedRR)) {
            throw new \InvalidArgumentException('Realized R:R must be numeric.');
        }
    }

    public function grossResult(): Money
    {
        return $this->grossResult;
    }

    public function netResult(): Money
    {
        return $this->netResult;
    }

    public function resultType(): ResultType
    {
        return $this->resultType;
    }

    public function realizedRR(): string
    {
        return $this->realizedRR;
    }

    public function isLoss(): bool
    {
        return $this->resultType === ResultType::LOSS;
    }

    public function equals(ValueObject $other): bool
    {
        return $other instanceof self
            && $this->grossResult->equals($other->grossResult)
            && $this->netResult->equals($other->netResult)
            && $this->resultType === $other->resultType
            && bccomp($this->realizedRR, $other->realizedRR, 8) === 0;
    }
}
