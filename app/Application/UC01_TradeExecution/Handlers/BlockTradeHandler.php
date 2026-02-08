<?php

declare(strict_types=1);

namespace App\Application\UC01_TradeExecution\Handlers;

use App\Application\Contracts\EventPublisher;
use App\Application\Contracts\TradeRepository;
use App\Application\UC01_TradeExecution\Commands\BlockTradeCommand;
use App\Domain\Trade\ValueObjects\Reason;

final class BlockTradeHandler
{
    public function __construct(
        private readonly TradeRepository $tradeRepository,
        private readonly EventPublisher $eventPublisher,
    ) {}

    public function handle(BlockTradeCommand $command): void
    {
        $trade = $this->tradeRepository->getById($command->tradeId);

        $reasons = array_map(
            fn (array $r) => new Reason($r['code'], $r['description']),
            $command->reasons,
        );

        $trade->block(...$reasons);

        $this->tradeRepository->save($trade);

        foreach ($trade->releaseEvents() as $event) {
            $this->eventPublisher->publish($event);
        }
    }
}
