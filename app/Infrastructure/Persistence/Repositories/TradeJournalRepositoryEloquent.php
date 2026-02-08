<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Application\Contracts\TradeJournalRepository;
use App\Domain\Journal\Aggregates\TradeRecord;
use App\Domain\Journal\Entities\TradeJournal;
use App\Domain\Journal\ValueObjects\DateRange;
use App\Domain\Journal\ValueObjects\EmotionalState;
use App\Domain\Journal\ValueObjects\Money;
use App\Domain\Journal\ValueObjects\Quantity;
use App\Domain\Journal\ValueObjects\ResultType;
use App\Domain\Journal\ValueObjects\TradeLesson;
use App\Domain\Journal\ValueObjects\TradeOutcome;
use App\Domain\Journal\ValueObjects\TradeRationale;
use App\Infrastructure\Persistence\Eloquent\TradeRecordModel;

final class TradeJournalRepositoryEloquent implements TradeJournalRepository
{
    public function save(TradeRecord $record): void
    {
        $journal = $record->journal();

        $data = [
            'user_id' => $journal->userId(),
            'trade_id' => $journal->tradeId(),
            'asset_symbol' => $journal->assetSymbol(),
            'entry_price_amount' => $journal->entryPrice()->amount(),
            'entry_price_currency' => $journal->entryPrice()->currency(),
            'quantity' => $journal->quantity()->value(),
            'period_start' => $journal->tradePeriod()->start(),
            'period_end' => $journal->tradePeriod()->end(),
            'reviewed' => $journal->isReviewed(),
        ];

        if ($journal->isReviewed() && $journal->outcome() !== null) {
            $data['gross_result_amount'] = $journal->outcome()->grossResult()->amount();
            $data['gross_result_currency'] = $journal->outcome()->grossResult()->currency();
            $data['net_result_amount'] = $journal->outcome()->netResult()->amount();
            $data['net_result_currency'] = $journal->outcome()->netResult()->currency();
            $data['result_type'] = $journal->outcome()->resultType()->value;
            $data['realized_rr'] = $journal->outcome()->realizedRR();
            $data['followed_plan'] = $journal->rationale()?->followedPlan();
            $data['deviation_reason'] = $journal->rationale()?->deviationReason();
            $data['emotional_state'] = $journal->emotionalState()?->value;
            $data['keep_doing'] = $journal->lesson()?->keepDoing();
            $data['improve_next_time'] = $journal->lesson()?->improveNextTime();
        }

        TradeRecordModel::updateOrCreate(
            ['id' => $record->id()],
            $data,
        );
    }

    public function getById(string $recordId): TradeRecord
    {
        $model = TradeRecordModel::findOrFail($recordId);

        return $this->hydrate($model);
    }

    public function getByTradeId(string $tradeId): TradeRecord
    {
        $model = TradeRecordModel::where('trade_id', $tradeId)->firstOrFail();

        return $this->hydrate($model);
    }

    /** @return TradeRecord[] */
    public function getByUserId(string $userId, ?DateRange $period = null): array
    {
        $query = TradeRecordModel::where('user_id', $userId);

        if ($period !== null) {
            $query->where('period_start', '>=', $period->start())
                ->where('period_end', '<=', $period->end());
        }

        $models = $query->orderBy('created_at', 'desc')->get();

        return $models->map(fn (TradeRecordModel $model) => $this->hydrate($model))->all();
    }

    private function hydrate(TradeRecordModel $model): TradeRecord
    {
        $outcome = null;
        $rationale = null;
        $emotionalState = null;
        $lesson = null;

        if ($model->reviewed && $model->result_type !== null) {
            $outcome = new TradeOutcome(
                new Money((string) $model->gross_result_amount, $model->gross_result_currency),
                new Money((string) $model->net_result_amount, $model->net_result_currency),
                ResultType::from($model->result_type),
                (string) $model->realized_rr,
            );

            $rationale = new TradeRationale(
                (bool) $model->followed_plan,
                $model->deviation_reason,
            );

            $emotionalState = EmotionalState::from($model->emotional_state);

            $lesson = new TradeLesson(
                $model->keep_doing,
                $model->improve_next_time,
            );
        }

        /** @var \DateTimeImmutable $periodStart */
        $periodStart = $model->period_start;

        /** @var \DateTimeImmutable $periodEnd */
        $periodEnd = $model->period_end;

        /** @var \DateTimeImmutable $createdAt */
        $createdAt = $model->created_at;

        $journal = TradeJournal::reconstitute(
            id: $model->id,
            userId: $model->user_id,
            tradeId: $model->trade_id,
            assetSymbol: $model->asset_symbol,
            entryPrice: new Money((string) $model->entry_price_amount, $model->entry_price_currency),
            quantity: new Quantity($model->quantity),
            tradePeriod: new DateRange($periodStart, $periodEnd),
            createdAt: $createdAt,
            outcome: $outcome,
            rationale: $rationale,
            emotionalState: $emotionalState,
            lesson: $lesson,
            reviewed: (bool) $model->reviewed,
        );

        return TradeRecord::reconstitute($journal);
    }
}
