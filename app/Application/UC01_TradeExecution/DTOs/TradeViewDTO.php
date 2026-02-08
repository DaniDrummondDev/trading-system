<?php

declare(strict_types=1);

namespace App\Application\UC01_TradeExecution\DTOs;

final readonly class TradeViewDTO
{
    public function __construct(
        public string $id,
        public string $assetSymbol,
        public string $market,
        public string $direction,
        public string $timeframe,
        public string $state,
        public ?string $entryPrice,
        public ?string $stopPrice,
        public ?string $targetPrice,
        public ?string $riskPercentage,
        public ?int $positionSize,
        public ?string $executedPrice,
        public ?int $executedQuantity,
        public ?string $result,
        public string $createdAt,
    ) {}
}
