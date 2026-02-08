<?php

declare(strict_types=1);

namespace App\Application\UC01_TradeExecution\Handlers;

use App\Application\Contracts\EventPublisher;
use App\Application\Contracts\TradeRepository;
use App\Application\UC01_TradeExecution\Commands\ExecuteTradeCommand;
use App\Domain\Trade\ValueObjects\Price;

final class ExecuteTradeHandler
{
    public function __construct(
        private readonly TradeRepository $tradeRepository,
        private readonly EventPublisher $eventPublisher,
    ) {}

    public function handle(ExecuteTradeCommand $command): void
    {
        $trade = $this->tradeRepository->getById($command->tradeId);

        $trade->execute(new Price($command->executedPrice), $command->quantity);

        $this->tradeRepository->save($trade);

        foreach ($trade->releaseEvents() as $event) {
            $this->eventPublisher->publish($event);
        }
    }
}
