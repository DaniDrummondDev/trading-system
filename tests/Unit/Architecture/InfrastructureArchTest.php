<?php

declare(strict_types=1);

arch('Infrastructure usa strict types em todos os arquivos')
    ->expect('App\Infrastructure')
    ->toUseStrictTypes();

arch('Eloquent Models são final')
    ->expect('App\Infrastructure\Persistence\Eloquent')
    ->classes()
    ->toBeFinal();

arch('Repositories são final')
    ->expect('App\Infrastructure\Persistence\Repositories')
    ->classes()
    ->toBeFinal();

arch('Services são final')
    ->expect('App\Infrastructure\Services')
    ->classes()
    ->toBeFinal();

arch('EventBus é final')
    ->expect('App\Infrastructure\EventBus')
    ->classes()
    ->toBeFinal();

arch('Infrastructure não depende de Interfaces')
    ->expect('App\Infrastructure')
    ->not->toUse('App\Interfaces');

arch('Domain não depende de Infrastructure')
    ->expect('App\Domain')
    ->not->toUse('App\Infrastructure');

arch('Application não depende de Infrastructure')
    ->expect('App\Application')
    ->not->toUse('App\Infrastructure');
