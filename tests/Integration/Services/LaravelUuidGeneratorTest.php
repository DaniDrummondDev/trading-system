<?php

declare(strict_types=1);

use App\Infrastructure\Services\LaravelUuidGenerator;

it('gera UUID válido', function () {
    $generator = new LaravelUuidGenerator;
    $uuid = $generator->generate();

    expect($uuid)->toMatch('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/');
});

it('gera UUIDs únicos', function () {
    $generator = new LaravelUuidGenerator;

    $uuids = [];
    for ($i = 0; $i < 100; $i++) {
        $uuids[] = $generator->generate();
    }

    expect(array_unique($uuids))->toHaveCount(100);
});
