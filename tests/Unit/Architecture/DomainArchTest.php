<?php

declare(strict_types=1);

arch('Domain usa strict types em todos os arquivos')
    ->expect('App\Domain')
    ->toUseStrictTypes();

arch('Domain não depende de Laravel')
    ->expect('App\Domain')
    ->not->toUse('Illuminate');

arch('Domain não depende de Infrastructure')
    ->expect('App\Domain')
    ->not->toUse('App\Infrastructure');

arch('Domain não depende de Application')
    ->expect('App\Domain')
    ->not->toUse('App\Application');

arch('Domain não depende de Interfaces')
    ->expect('App\Domain')
    ->not->toUse('App\Interfaces');

arch('Value Objects do Trade são final')
    ->expect('App\Domain\Trade\ValueObjects')
    ->classes()
    ->toBeFinal();

arch('Value Objects do Journal são final')
    ->expect('App\Domain\Journal\ValueObjects')
    ->classes()
    ->toBeFinal();

arch('Value Objects do Metrics são final')
    ->expect('App\Domain\Metrics\ValueObjects')
    ->classes()
    ->toBeFinal();

arch('Events do Trade são final e implementam DomainEvent')
    ->expect('App\Domain\Trade\Events')
    ->classes()
    ->toBeFinal()
    ->toImplement('App\Domain\Shared\Events\DomainEvent');

arch('Events do Journal são final e implementam DomainEvent')
    ->expect('App\Domain\Journal\Events')
    ->classes()
    ->toBeFinal()
    ->toImplement('App\Domain\Shared\Events\DomainEvent');

arch('TradeAggregate é final e extends AggregateRoot')
    ->expect('App\Domain\Trade\Aggregates\TradeAggregate')
    ->toBeFinal()
    ->toExtend('App\Domain\Shared\AggregateRoot');

arch('TradeRecord é final e extends AggregateRoot')
    ->expect('App\Domain\Journal\Aggregates\TradeRecord')
    ->toBeFinal()
    ->toExtend('App\Domain\Shared\AggregateRoot');
