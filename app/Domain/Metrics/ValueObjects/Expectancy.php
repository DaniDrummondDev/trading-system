<?php

declare(strict_types=1);

namespace App\Domain\Metrics\ValueObjects;

use App\Domain\Shared\ValueObject;

/**
 * Expectância: (WR × AvgGain) − (LR × AvgLoss)
 * Pode ser negativa (sistema perdedor).
 */
final class Expectancy extends ValueObject
{
    public function __construct(
        private readonly string $value,
    ) {
        if (! is_numeric($this->value)) {
            throw new \InvalidArgumentException('Expectancy must be numeric.');
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function isPositive(): bool
    {
        return bccomp($this->value, '0', 8) > 0;
    }

    public function equals(ValueObject $other): bool
    {
        return $other instanceof self
            && bccomp($this->value, $other->value, 8) === 0;
    }
}
