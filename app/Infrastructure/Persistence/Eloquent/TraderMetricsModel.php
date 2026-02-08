<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;

final class TraderMetricsModel extends Model
{
    protected $table = 'trader_metrics';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'user_id',
        'period',
        'win_rate',
        'expectancy',
        'profit_factor',
        'max_drawdown',
        'plan_discipline_score',
        'emotional_stability_index',
        'calculated_at',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'calculated_at' => 'immutable_datetime',
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
        ];
    }
}
