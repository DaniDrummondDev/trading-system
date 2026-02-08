<?php

declare(strict_types=1);

namespace App\Domain\Trade\ValueObjects;

use App\Domain\Shared\ValueObject;

final class PriceLevel extends ValueObject
{
    public function __construct(
        private readonly Price $price,
        private readonly PriceLevelType $type,
    ) {}

    public function price(): Price
    {
        return $this->price;
    }

    public function type(): PriceLevelType
    {
        return $this->type;
    }

    public function equals(ValueObject $other): bool
    {
        return $other instanceof self
            && $this->price->equals($other->price)
            && $this->type === $other->type;
    }
}
