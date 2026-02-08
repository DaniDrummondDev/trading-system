<?php

declare(strict_types=1);

namespace App\Application\TradeJournal\DTOs;

final readonly class TradeRecordViewDTO
{
    public function __construct(
        public string $id,
        public string $tradeId,
        public string $assetSymbol,
        public string $entryPrice,
        public string $currency,
        public int $quantity,
        public string $periodStart,
        public string $periodEnd,
        public bool $isReviewed,
        public bool $hasAttentionFlag,
        public bool $hasSetupInvalidation,
        public ?string $resultType,
        public ?string $grossResult,
        public ?string $netResult,
        public ?string $realizedRR,
        public ?bool $followedPlan,
        public ?string $emotionalState,
        public ?string $keepDoing,
        public ?string $improveNextTime,
        public string $createdAt,
    ) {}
}
