<?php

declare(strict_types=1);

namespace App\Application\UC02_TradeJournal\Handlers;

use App\Application\Contracts\EventPublisher;
use App\Application\Contracts\TradeJournalRepository;
use App\Application\Contracts\TradeRepository;
use App\Application\Contracts\UuidGenerator;
use App\Application\UC02_TradeJournal\Commands\RegisterTradeExecutionCommand;
use App\Application\UC02_TradeJournal\DTOs\TradeRecordCreatedDTO;
use App\Domain\Journal\Aggregates\TradeRecord;
use App\Domain\Journal\ValueObjects\DateRange;
use App\Domain\Journal\ValueObjects\Money;
use App\Domain\Journal\ValueObjects\Quantity;
use App\Domain\Trade\ValueObjects\TradeState;

final class RegisterTradeExecutionHandler
{
    public function __construct(
        private readonly TradeRepository $tradeRepository,
        private readonly TradeJournalRepository $journalRepository,
        private readonly EventPublisher $eventPublisher,
        private readonly UuidGenerator $uuidGenerator,
    ) {}

    public function handle(RegisterTradeExecutionCommand $command): TradeRecordCreatedDTO
    {
        $trade = $this->tradeRepository->getById($command->tradeId);

        if ($trade->state() !== TradeState::EXECUTED) {
            throw new \DomainException('Trade must be in EXECUTED state to register journal.');
        }

        $recordId = $this->uuidGenerator->generate();

        $record = TradeRecord::create(
            $recordId,
            $command->tradeId,
            $trade->asset()->symbol(),
            new Money($command->entryPrice, $command->currency),
            new Quantity($command->quantity),
            new DateRange(
                new \DateTimeImmutable($command->entryDate),
                new \DateTimeImmutable($command->exitDate),
            ),
        );

        $this->journalRepository->save($record);

        foreach ($record->releaseEvents() as $event) {
            $this->eventPublisher->publish($event);
        }

        return new TradeRecordCreatedDTO($recordId);
    }
}
