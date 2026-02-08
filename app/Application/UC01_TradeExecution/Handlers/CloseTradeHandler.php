<?php

declare(strict_types=1);

namespace App\Application\UC01_TradeExecution\Handlers;

use App\Application\Contracts\EventPublisher;
use App\Application\Contracts\TradeRepository;
use App\Application\UC01_TradeExecution\Commands\CloseTradeCommand;

final class CloseTradeHandler
{
    public function __construct(
        private readonly TradeRepository $tradeRepository,
        private readonly EventPublisher $eventPublisher,
    ) {}

    public function handle(CloseTradeCommand $command): void
    {
        $trade = $this->tradeRepository->getById($command->tradeId);

        $trade->close($command->result);

        $this->tradeRepository->save($trade);

        foreach ($trade->releaseEvents() as $event) {
            $this->eventPublisher->publish($event);
        }
    }
}
