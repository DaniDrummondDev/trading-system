<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;

final class TradeRecordModel extends Model
{
    protected $table = 'trade_records';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'user_id',
        'trade_id',
        'asset_symbol',
        'entry_price_amount',
        'entry_price_currency',
        'quantity',
        'period_start',
        'period_end',
        'gross_result_amount',
        'gross_result_currency',
        'net_result_amount',
        'net_result_currency',
        'result_type',
        'realized_rr',
        'followed_plan',
        'deviation_reason',
        'emotional_state',
        'keep_doing',
        'improve_next_time',
        'reviewed',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'followed_plan' => 'boolean',
            'reviewed' => 'boolean',
            'period_start' => 'immutable_datetime',
            'period_end' => 'immutable_datetime',
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
        ];
    }
}
