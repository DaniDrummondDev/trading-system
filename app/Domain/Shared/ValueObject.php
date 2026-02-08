<?php

declare(strict_types=1);

namespace App\Domain\Shared;

abstract class ValueObject
{
    abstract public function equals(self $other): bool;
}
