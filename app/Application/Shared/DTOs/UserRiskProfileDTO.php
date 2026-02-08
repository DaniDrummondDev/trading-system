<?php

declare(strict_types=1);

namespace App\Application\Shared\DTOs;

final readonly class UserRiskProfileDTO
{
    public function __construct(
        public string $userId,
        public string $maxRiskPerTrade,
        public string $accountSize,
        public string $maxDailyLoss,
        public int $maxSimultaneousTrades,
    ) {}
}
