<?php

declare(strict_types=1);

namespace App\Domain\Trade\ValueObjects;

use App\Domain\Shared\ValueObject;

final class Reason extends ValueObject
{
    public function __construct(
        private readonly string $code,
        private readonly string $description,
    ) {
        if (trim($this->code) === '') {
            throw new \InvalidArgumentException('Reason code cannot be empty.');
        }

        if (trim($this->description) === '') {
            throw new \InvalidArgumentException('Reason description cannot be empty.');
        }
    }

    public function code(): string
    {
        return $this->code;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function equals(ValueObject $other): bool
    {
        return $other instanceof self
            && $this->code === $other->code
            && $this->description === $other->description;
    }
}
