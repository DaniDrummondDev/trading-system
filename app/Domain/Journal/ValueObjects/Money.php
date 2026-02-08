<?php

declare(strict_types=1);

namespace App\Domain\Journal\ValueObjects;

use App\Domain\Shared\ValueObject;

final class Money extends ValueObject
{
    private const int SCALE = 8;

    public function __construct(
        private readonly string $amount,
        private readonly string $currency = 'BRL',
    ) {
        if (! is_numeric($this->amount)) {
            throw new \InvalidArgumentException('Money amount must be numeric.');
        }

        if (trim($this->currency) === '') {
            throw new \InvalidArgumentException('Currency cannot be empty.');
        }
    }

    public function amount(): string
    {
        return $this->amount;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function add(self $other): self
    {
        $this->guardSameCurrency($other);

        return new self(bcadd($this->amount, $other->amount, self::SCALE), $this->currency);
    }

    public function subtract(self $other): self
    {
        $this->guardSameCurrency($other);

        return new self(bcsub($this->amount, $other->amount, self::SCALE), $this->currency);
    }

    public function isPositive(): bool
    {
        return bccomp($this->amount, '0', self::SCALE) > 0;
    }

    public function isNegative(): bool
    {
        return bccomp($this->amount, '0', self::SCALE) < 0;
    }

    public function isZero(): bool
    {
        return bccomp($this->amount, '0', self::SCALE) === 0;
    }

    public function equals(ValueObject $other): bool
    {
        return $other instanceof self
            && bccomp($this->amount, $other->amount, self::SCALE) === 0
            && $this->currency === $other->currency;
    }

    private function guardSameCurrency(self $other): void
    {
        if ($this->currency !== $other->currency) {
            throw new \InvalidArgumentException(
                "Não é possível operar moedas diferentes: {$this->currency} vs {$other->currency}"
            );
        }
    }
}
