<?php

declare(strict_types=1);

namespace App\Domain\Trade\ValueObjects;

use App\Domain\Shared\ValueObject;

final class Asset extends ValueObject
{
    private readonly string $symbol;

    public function __construct(
        string $symbol,
        private readonly string $market,
    ) {
        $symbol = strtoupper(trim($symbol));

        if ($symbol === '') {
            throw new \InvalidArgumentException('Asset symbol cannot be empty.');
        }

        if (trim($this->market) === '') {
            throw new \InvalidArgumentException('Asset market cannot be empty.');
        }

        $this->symbol = $symbol;
    }

    public function symbol(): string
    {
        return $this->symbol;
    }

    public function market(): string
    {
        return $this->market;
    }

    public function equals(ValueObject $other): bool
    {
        return $other instanceof self
            && $this->symbol === $other->symbol
            && $this->market === $other->market;
    }
}
