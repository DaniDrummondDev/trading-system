<?php

declare(strict_types=1);

namespace App\Application\TradeExecution\Commands;

final readonly class BlockTradeCommand
{
    /**
     * @param  array<array{code: string, description: string}>  $reasons
     */
    public function __construct(
        public string $tradeId,
        public array $reasons,
    ) {}
}
