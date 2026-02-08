<?php

declare(strict_types=1);

namespace App\Domain\Journal\ValueObjects;

use App\Domain\Shared\ValueObject;

final class Quantity extends ValueObject
{
    public function __construct(
        private readonly int $value,
    ) {
        if ($this->value <= 0) {
            throw new \InvalidArgumentException('Quantity must be a positive integer.');
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(ValueObject $other): bool
    {
        return $other instanceof self
            && $this->value === $other->value;
    }
}
