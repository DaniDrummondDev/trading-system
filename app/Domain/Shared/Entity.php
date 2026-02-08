<?php

declare(strict_types=1);

namespace App\Domain\Shared;

abstract class Entity
{
    public function __construct(
        private readonly string $id,
    ) {}

    public function id(): string
    {
        return $this->id;
    }

    public function equals(self $other): bool
    {
        return static::class === $other::class
            && $this->id === $other->id;
    }
}
