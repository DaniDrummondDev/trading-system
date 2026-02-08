<?php

declare(strict_types=1);

namespace App\Application\TradeExecution\DTOs;

final readonly class TradeCreatedDTO
{
    public function __construct(
        public string $tradeId,
    ) {}
}
