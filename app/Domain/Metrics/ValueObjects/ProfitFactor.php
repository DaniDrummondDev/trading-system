<?php

declare(strict_types=1);

namespace App\Domain\Metrics\ValueObjects;

use App\Domain\Shared\ValueObject;

/**
 * Profit Factor: soma dos ganhos ÷ soma das perdas.
 * Valores >= 0. Acima de 1.5 é considerado bom.
 */
final class ProfitFactor extends ValueObject
{
    public function __construct(
        private readonly string $value,
    ) {
        if (! is_numeric($this->value)) {
            throw new \InvalidArgumentException('ProfitFactor must be numeric.');
        }

        if (bccomp($this->value, '0', 8) < 0) {
            throw new \InvalidArgumentException('ProfitFactor must be >= 0.');
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(ValueObject $other): bool
    {
        return $other instanceof self
            && bccomp($this->value, $other->value, 8) === 0;
    }
}
