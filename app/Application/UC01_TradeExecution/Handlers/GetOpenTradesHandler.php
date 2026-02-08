<?php

declare(strict_types=1);

namespace App\Application\UC01_TradeExecution\Handlers;

use App\Application\Contracts\TradeRepository;
use App\Application\UC01_TradeExecution\DTOs\TradeViewDTO;
use App\Application\UC01_TradeExecution\Queries\GetOpenTradesQuery;
use App\Domain\Trade\Aggregates\TradeAggregate;

final class GetOpenTradesHandler
{
    public function __construct(
        private readonly TradeRepository $tradeRepository,
    ) {}

    /**
     * @return TradeViewDTO[]
     */
    public function handle(GetOpenTradesQuery $query): array
    {
        $trades = $this->tradeRepository->getOpenTrades($query->userId);

        return array_map(fn (TradeAggregate $trade) => $this->toDTO($trade), $trades);
    }

    private function toDTO(TradeAggregate $trade): TradeViewDTO
    {
        $entity = $trade->trade();

        return new TradeViewDTO(
            id: $trade->id(),
            assetSymbol: $entity->asset()->symbol(),
            market: $entity->asset()->market(),
            direction: $entity->direction()->value,
            timeframe: $entity->timeframe()->value,
            state: $entity->state()->value,
            entryPrice: $entity->entry()?->price()->amount(),
            stopPrice: $entity->stop()?->price()->amount(),
            targetPrice: $entity->target()?->price()->amount(),
            riskPercentage: $entity->riskPercentage(),
            positionSize: $entity->positionSize(),
            executedPrice: $entity->executedPrice()?->amount(),
            executedQuantity: $entity->executedQuantity(),
            result: $entity->result(),
            createdAt: $entity->createdAt()->format('c'),
        );
    }
}
