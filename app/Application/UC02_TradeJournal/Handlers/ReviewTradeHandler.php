<?php

declare(strict_types=1);

namespace App\Application\UC02_TradeJournal\Handlers;

use App\Application\Contracts\EventPublisher;
use App\Application\Contracts\TradeJournalRepository;
use App\Application\UC02_TradeJournal\Commands\ReviewTradeCommand;
use App\Domain\Journal\ValueObjects\EmotionalState;
use App\Domain\Journal\ValueObjects\Money;
use App\Domain\Journal\ValueObjects\ResultType;
use App\Domain\Journal\ValueObjects\TradeLesson;
use App\Domain\Journal\ValueObjects\TradeOutcome;
use App\Domain\Journal\ValueObjects\TradeRationale;

final class ReviewTradeHandler
{
    public function __construct(
        private readonly TradeJournalRepository $journalRepository,
        private readonly EventPublisher $eventPublisher,
    ) {}

    public function handle(ReviewTradeCommand $command): void
    {
        $record = $this->journalRepository->getById($command->recordId);

        $outcome = new TradeOutcome(
            new Money($command->grossResult),
            new Money($command->netResult),
            ResultType::from($command->resultType),
            $command->realizedRR,
        );

        $rationale = new TradeRationale(
            $command->followedPlan,
            $command->deviationReason,
        );

        $emotionalState = EmotionalState::from($command->emotionalState);

        $lesson = new TradeLesson(
            $command->keepDoing,
            $command->improveNextTime,
        );

        $record->review($outcome, $rationale, $emotionalState, $lesson);

        $this->journalRepository->save($record);

        foreach ($record->releaseEvents() as $event) {
            $this->eventPublisher->publish($event);
        }
    }
}
