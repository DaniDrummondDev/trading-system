<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Application\Contracts\TradeRepository;
use App\Domain\Trade\Aggregates\TradeAggregate;
use App\Domain\Trade\Entities\Trade;
use App\Domain\Trade\ValueObjects\Asset;
use App\Domain\Trade\ValueObjects\Price;
use App\Domain\Trade\ValueObjects\PriceLevel;
use App\Domain\Trade\ValueObjects\PriceLevelType;
use App\Domain\Trade\ValueObjects\Timeframe;
use App\Domain\Trade\ValueObjects\TradeDirection;
use App\Domain\Trade\ValueObjects\TradeState;
use App\Infrastructure\Persistence\Eloquent\TradeDecisionModel;

final class TradeRepositoryEloquent implements TradeRepository
{
    public function save(TradeAggregate $trade): void
    {
        $entity = $trade->trade();

        TradeDecisionModel::updateOrCreate(
            ['id' => $trade->id()],
            [
                'user_id' => $entity->userId(),
                'asset_symbol' => $entity->asset()->symbol(),
                'asset_market' => $entity->asset()->market(),
                'direction' => $entity->direction()->value,
                'timeframe' => $entity->timeframe()->value,
                'state' => $entity->state()->value,
                'entry_price' => $entity->entry()?->price()->amount(),
                'entry_price_type' => $entity->entry()?->type()->value,
                'stop_price' => $entity->stop()?->price()->amount(),
                'stop_price_type' => $entity->stop()?->type()->value,
                'target_price' => $entity->target()?->price()->amount(),
                'target_price_type' => $entity->target()?->type()->value,
                'risk_percentage' => $entity->riskPercentage(),
                'position_size' => $entity->positionSize(),
                'executed_price' => $entity->executedPrice()?->amount(),
                'executed_quantity' => $entity->executedQuantity(),
                'executed_at' => $entity->executedAt(),
                'result' => $entity->result(),
                'closed_at' => $entity->closedAt(),
            ],
        );
    }

    public function getById(string $tradeId): TradeAggregate
    {
        $model = TradeDecisionModel::findOrFail($tradeId);

        return $this->hydrate($model);
    }

    /** @return TradeAggregate[] */
    public function getOpenTrades(string $userId): array
    {
        $models = TradeDecisionModel::where('user_id', $userId)
            ->whereNotIn('state', [
                TradeState::CLOSED->value,
                TradeState::BLOCKED->value,
                TradeState::EXPIRED->value,
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return $models->map(fn (TradeDecisionModel $model) => $this->hydrate($model))->all();
    }

    private function hydrate(TradeDecisionModel $model): TradeAggregate
    {
        $entry = $model->entry_price !== null
            ? new PriceLevel(new Price((string) $model->entry_price), PriceLevelType::from($model->entry_price_type))
            : null;

        $stop = $model->stop_price !== null
            ? new PriceLevel(new Price((string) $model->stop_price), PriceLevelType::from($model->stop_price_type))
            : null;

        $target = $model->target_price !== null
            ? new PriceLevel(new Price((string) $model->target_price), PriceLevelType::from($model->target_price_type))
            : null;

        /** @var \DateTimeImmutable $createdAt */
        $createdAt = $model->created_at;

        /** @var \DateTimeImmutable|null $executedAt */
        $executedAt = $model->executed_at;

        /** @var \DateTimeImmutable|null $closedAt */
        $closedAt = $model->closed_at;

        $trade = Trade::reconstitute(
            id: $model->id,
            userId: $model->user_id,
            asset: new Asset($model->asset_symbol, $model->asset_market),
            direction: TradeDirection::from($model->direction),
            timeframe: Timeframe::from($model->timeframe),
            state: TradeState::from($model->state),
            createdAt: $createdAt,
            entry: $entry,
            stop: $stop,
            target: $target,
            riskPercentage: $model->risk_percentage !== null ? (string) $model->risk_percentage : null,
            positionSize: $model->position_size,
            executedPrice: $model->executed_price !== null ? new Price((string) $model->executed_price) : null,
            executedQuantity: $model->executed_quantity,
            executedAt: $executedAt,
            result: $model->result,
            closedAt: $closedAt,
        );

        return TradeAggregate::reconstitute($trade);
    }
}
