<?php

declare(strict_types=1);

namespace App\Infrastructure\Services;

use App\Application\Contracts\UuidGenerator;
use Illuminate\Support\Str;

final class LaravelUuidGenerator implements UuidGenerator
{
    public function generate(): string
    {
        return (string) Str::uuid();
    }
}
