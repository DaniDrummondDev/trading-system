<?php

declare(strict_types=1);

namespace App\Application\Contracts;

interface UuidGenerator
{
    public function generate(): string;
}
