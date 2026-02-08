<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;

final class AiAnalysisModel extends Model
{
    protected $table = 'ai_analyses';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'trade_id',
        'analysis_type',
        'content',
        'embedding',
        'metadata',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
        ];
    }
}
