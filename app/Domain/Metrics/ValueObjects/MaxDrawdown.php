<?php

declare(strict_types=1);

namespace App\Domain\Metrics\ValueObjects;

use App\Domain\Shared\ValueObject;

/**
 * Max Drawdown: maior declínio acumulado de capital.
 * Valor entre 0 e 1 (percentual).
 */
final class MaxDrawdown extends ValueObject
{
    public function __construct(
        private readonly string $value,
    ) {
        if (! is_numeric($this->value)) {
            throw new \InvalidArgumentException('MaxDrawdown must be numeric.');
        }

        if (bccomp($this->value, '0', 8) < 0 || bccomp($this->value, '1', 8) > 0) {
            throw new \InvalidArgumentException('MaxDrawdown must be between 0 and 1.');
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function asPercentage(): string
    {
        return bcmul($this->value, '100', 2);
    }

    public function equals(ValueObject $other): bool
    {
        return $other instanceof self
            && bccomp($this->value, $other->value, 8) === 0;
    }
}
