<?php

declare(strict_types=1);

namespace App\Application\TradeExecution\Handlers;

use App\Application\Contracts\EventPublisher;
use App\Application\Contracts\TradeRepository;
use App\Application\TradeExecution\Commands\ExpireTradeCommand;

final class ExpireTradeHandler
{
    public function __construct(
        private readonly TradeRepository $tradeRepository,
        private readonly EventPublisher $eventPublisher,
    ) {}

    public function handle(ExpireTradeCommand $command): void
    {
        $trade = $this->tradeRepository->getById($command->tradeId);

        $trade->expire($command->reason);

        $this->tradeRepository->save($trade);

        foreach ($trade->releaseEvents() as $event) {
            $this->eventPublisher->publish($event);
        }
    }
}
