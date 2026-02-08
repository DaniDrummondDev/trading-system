<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;

final class DomainEventModel extends Model
{
    protected $table = 'domain_events';

    protected $primaryKey = 'event_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'event_id',
        'event_type',
        'aggregate_id',
        'aggregate_type',
        'payload',
        'occurred_on',
        'created_at',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'occurred_on' => 'immutable_datetime',
            'created_at' => 'immutable_datetime',
        ];
    }
}
