<?php

declare(strict_types=1);

namespace App\Domain\Journal\ValueObjects;

use App\Domain\Shared\ValueObject;

final class TradeLesson extends ValueObject
{
    public function __construct(
        private readonly string $keepDoing,
        private readonly string $improveNextTime,
    ) {
        if (trim($this->keepDoing) === '') {
            throw new \InvalidArgumentException('Keep doing cannot be empty.');
        }

        if (trim($this->improveNextTime) === '') {
            throw new \InvalidArgumentException('Improve next time cannot be empty.');
        }
    }

    public function keepDoing(): string
    {
        return $this->keepDoing;
    }

    public function improveNextTime(): string
    {
        return $this->improveNextTime;
    }

    public function equals(ValueObject $other): bool
    {
        return $other instanceof self
            && $this->keepDoing === $other->keepDoing
            && $this->improveNextTime === $other->improveNextTime;
    }
}
