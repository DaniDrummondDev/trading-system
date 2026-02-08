<?php

declare(strict_types=1);

namespace App\Application\TradeExecution\Handlers;

use App\Application\Contracts\EventPublisher;
use App\Application\Contracts\TradeRepository;
use App\Application\TradeExecution\Commands\ApproveTradeCommand;

final class ApproveTradeHandler
{
    public function __construct(
        private readonly TradeRepository $tradeRepository,
        private readonly EventPublisher $eventPublisher,
    ) {}

    public function handle(ApproveTradeCommand $command): void
    {
        $trade = $this->tradeRepository->getById($command->tradeId);

        $trade->approve();

        $this->tradeRepository->save($trade);

        foreach ($trade->releaseEvents() as $event) {
            $this->eventPublisher->publish($event);
        }
    }
}
