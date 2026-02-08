<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;

final class TradeDecisionModel extends Model
{
    protected $table = 'trade_decisions';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'user_id',
        'asset_symbol',
        'asset_market',
        'direction',
        'timeframe',
        'state',
        'entry_price',
        'entry_price_type',
        'stop_price',
        'stop_price_type',
        'target_price',
        'target_price_type',
        'risk_percentage',
        'position_size',
        'executed_price',
        'executed_quantity',
        'executed_at',
        'result',
        'closed_at',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'position_size' => 'integer',
            'executed_quantity' => 'integer',
            'executed_at' => 'immutable_datetime',
            'closed_at' => 'immutable_datetime',
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
        ];
    }
}
