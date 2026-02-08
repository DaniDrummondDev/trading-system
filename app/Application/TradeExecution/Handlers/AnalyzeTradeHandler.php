<?php

declare(strict_types=1);

namespace App\Application\TradeExecution\Handlers;

use App\Application\Contracts\EventPublisher;
use App\Application\Contracts\TradeRepository;
use App\Application\TradeExecution\Commands\AnalyzeTradeCommand;
use App\Domain\Trade\ValueObjects\Price;
use App\Domain\Trade\ValueObjects\PriceLevel;
use App\Domain\Trade\ValueObjects\PriceLevelType;

final class AnalyzeTradeHandler
{
    public function __construct(
        private readonly TradeRepository $tradeRepository,
        private readonly EventPublisher $eventPublisher,
    ) {}

    public function handle(AnalyzeTradeCommand $command): void
    {
        $trade = $this->tradeRepository->getById($command->tradeId);

        $trade->analyze(
            new PriceLevel(new Price($command->entryPrice), PriceLevelType::ENTRY),
            new PriceLevel(new Price($command->stopPrice), PriceLevelType::STOP),
            new PriceLevel(new Price($command->targetPrice), PriceLevelType::TARGET),
        );

        $this->tradeRepository->save($trade);

        foreach ($trade->releaseEvents() as $event) {
            $this->eventPublisher->publish($event);
        }
    }
}
