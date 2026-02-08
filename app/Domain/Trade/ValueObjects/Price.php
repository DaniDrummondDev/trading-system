<?php

declare(strict_types=1);

namespace App\Domain\Trade\ValueObjects;

use App\Domain\Shared\ValueObject;

final class Price extends ValueObject
{
    private const int SCALE = 8;

    public function __construct(
        private readonly string $amount,
    ) {
        if (! is_numeric($this->amount)) {
            throw new \InvalidArgumentException('Price amount must be numeric.');
        }

        if (bccomp($this->amount, '0', self::SCALE) <= 0) {
            throw new \InvalidArgumentException('Price must be positive.');
        }
    }

    public function amount(): string
    {
        return $this->amount;
    }

    public function isGreaterThan(self $other): bool
    {
        return bccomp($this->amount, $other->amount, self::SCALE) > 0;
    }

    public function isLessThan(self $other): bool
    {
        return bccomp($this->amount, $other->amount, self::SCALE) < 0;
    }

    public function add(self $other): self
    {
        return new self(bcadd($this->amount, $other->amount, self::SCALE));
    }

    public function subtract(self $other): self
    {
        $result = bcsub($this->amount, $other->amount, self::SCALE);

        if (bccomp($result, '0', self::SCALE) <= 0) {
            throw new \InvalidArgumentException('Subtraction would result in non-positive price.');
        }

        return new self($result);
    }

    public function equals(ValueObject $other): bool
    {
        return $other instanceof self
            && bccomp($this->amount, $other->amount, self::SCALE) === 0;
    }
}
