<?php

declare(strict_types=1);

arch('Application usa strict types em todos os arquivos')
    ->expect('App\Application')
    ->toUseStrictTypes();

arch('Application não depende de Laravel')
    ->expect('App\Application')
    ->not->toUse('Illuminate');

arch('Application não depende de Infrastructure')
    ->expect('App\Application')
    ->not->toUse('App\Infrastructure');

arch('Application não depende de Interfaces')
    ->expect('App\Application')
    ->not->toUse('App\Interfaces');

arch('Commands UC01 são final e readonly')
    ->expect('App\Application\TradeExecution\Commands')
    ->classes()
    ->toBeFinal()
    ->toBeReadonly();

arch('Commands UC02 são final e readonly')
    ->expect('App\Application\TradeJournal\Commands')
    ->classes()
    ->toBeFinal()
    ->toBeReadonly();

arch('Queries UC01 são final e readonly')
    ->expect('App\Application\TradeExecution\Queries')
    ->classes()
    ->toBeFinal()
    ->toBeReadonly();

arch('Queries UC02 são final e readonly')
    ->expect('App\Application\TradeJournal\Queries')
    ->classes()
    ->toBeFinal()
    ->toBeReadonly();

arch('DTOs UC01 são final e readonly')
    ->expect('App\Application\TradeExecution\DTOs')
    ->classes()
    ->toBeFinal()
    ->toBeReadonly();

arch('DTOs UC02 são final e readonly')
    ->expect('App\Application\TradeJournal\DTOs')
    ->classes()
    ->toBeFinal()
    ->toBeReadonly();

arch('DTOs Shared são final e readonly')
    ->expect('App\Application\Shared\DTOs')
    ->classes()
    ->toBeFinal()
    ->toBeReadonly();

arch('Handlers UC01 são final')
    ->expect('App\Application\TradeExecution\Handlers')
    ->classes()
    ->toBeFinal();

arch('Handlers UC02 são final')
    ->expect('App\Application\TradeJournal\Handlers')
    ->classes()
    ->toBeFinal();
