<?php

declare(strict_types=1);

namespace App\Domain\Metrics\ValueObjects;

use App\Domain\Shared\ValueObject;

final class KPI extends ValueObject
{
    public function __construct(
        private readonly string $name,
        private readonly string $value,
        private readonly string $period,
    ) {
        if (trim($this->name) === '') {
            throw new \InvalidArgumentException('KPI name cannot be empty.');
        }

        if (! is_numeric($this->value)) {
            throw new \InvalidArgumentException('KPI value must be numeric.');
        }

        if (trim($this->period) === '') {
            throw new \InvalidArgumentException('KPI period cannot be empty.');
        }
    }

    public function name(): string
    {
        return $this->name;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function period(): string
    {
        return $this->period;
    }

    public function equals(ValueObject $other): bool
    {
        return $other instanceof self
            && $this->name === $other->name
            && bccomp($this->value, $other->value, 8) === 0
            && $this->period === $other->period;
    }
}
