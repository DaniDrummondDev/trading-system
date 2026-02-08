<?php

declare(strict_types=1);

namespace App\Application\TradeExecution\Handlers;

use App\Application\Contracts\EventPublisher;
use App\Application\Contracts\TradeRepository;
use App\Application\TradeExecution\Commands\ValidateTradeRiskCommand;

final class ValidateTradeRiskHandler
{
    public function __construct(
        private readonly TradeRepository $tradeRepository,
        private readonly EventPublisher $eventPublisher,
    ) {}

    public function handle(ValidateTradeRiskCommand $command): void
    {
        $trade = $this->tradeRepository->getById($command->tradeId);

        $trade->validateRisk($command->riskPercentage, $command->positionSize);

        $this->tradeRepository->save($trade);

        foreach ($trade->releaseEvents() as $event) {
            $this->eventPublisher->publish($event);
        }
    }
}
