<?php

declare(strict_types=1);

namespace App\Application\UC02_TradeJournal\Commands;

final readonly class ReviewTradeCommand
{
    public function __construct(
        public string $recordId,
        public string $grossResult,
        public string $netResult,
        public string $resultType,
        public string $realizedRR,
        public bool $followedPlan,
        public ?string $deviationReason,
        public string $emotionalState,
        public string $keepDoing,
        public string $improveNextTime,
    ) {}
}
