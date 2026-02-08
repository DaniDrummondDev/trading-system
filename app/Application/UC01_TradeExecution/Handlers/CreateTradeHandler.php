<?php

declare(strict_types=1);

namespace App\Application\UC01_TradeExecution\Handlers;

use App\Application\Contracts\EventPublisher;
use App\Application\Contracts\TradeRepository;
use App\Application\Contracts\UuidGenerator;
use App\Application\UC01_TradeExecution\Commands\CreateTradeCommand;
use App\Application\UC01_TradeExecution\DTOs\TradeCreatedDTO;
use App\Domain\Trade\Aggregates\TradeAggregate;
use App\Domain\Trade\ValueObjects\Asset;
use App\Domain\Trade\ValueObjects\Timeframe;
use App\Domain\Trade\ValueObjects\TradeDirection;

final class CreateTradeHandler
{
    public function __construct(
        private readonly TradeRepository $tradeRepository,
        private readonly EventPublisher $eventPublisher,
        private readonly UuidGenerator $uuidGenerator,
    ) {}

    public function handle(CreateTradeCommand $command): TradeCreatedDTO
    {
        $tradeId = $this->uuidGenerator->generate();

        $trade = TradeAggregate::create(
            $tradeId,
            new Asset($command->assetSymbol, $command->market),
            TradeDirection::from($command->direction),
            Timeframe::from($command->timeframe),
        );

        $this->tradeRepository->save($trade);

        foreach ($trade->releaseEvents() as $event) {
            $this->eventPublisher->publish($event);
        }

        return new TradeCreatedDTO($tradeId);
    }
}
