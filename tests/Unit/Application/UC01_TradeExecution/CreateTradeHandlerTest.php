<?php

declare(strict_types=1);

use App\Application\Contracts\EventPublisher;
use App\Application\Contracts\TradeRepository;
use App\Application\Contracts\UuidGenerator;
use App\Application\UC01_TradeExecution\Commands\CreateTradeCommand;
use App\Application\UC01_TradeExecution\DTOs\TradeCreatedDTO;
use App\Application\UC01_TradeExecution\Handlers\CreateTradeHandler;
use App\Domain\Trade\Aggregates\TradeAggregate;
use App\Domain\Trade\Events\TradeCreated;

it('cria trade e retorna ID gerado', function () {
    $repository = Mockery::mock(TradeRepository::class);
    $publisher = Mockery::mock(EventPublisher::class);
    $uuidGenerator = Mockery::mock(UuidGenerator::class);

    $uuidGenerator->shouldReceive('generate')->once()->andReturn('uuid-001');
    $repository->shouldReceive('save')->once()->with(Mockery::type(TradeAggregate::class));
    $publisher->shouldReceive('publish')->once();

    $handler = new CreateTradeHandler($repository, $publisher, $uuidGenerator);

    $result = $handler->handle(new CreateTradeCommand(
        userId: 'user-001',
        assetSymbol: 'PETR4',
        market: 'B3',
        direction: 'LONG',
        timeframe: 'D1',
    ));

    expect($result)->toBeInstanceOf(TradeCreatedDTO::class)
        ->and($result->tradeId)->toBe('uuid-001');
});

it('publica evento TradeCreated após salvar', function () {
    $repository = Mockery::mock(TradeRepository::class);
    $publisher = Mockery::mock(EventPublisher::class);
    $uuidGenerator = Mockery::mock(UuidGenerator::class);

    $uuidGenerator->shouldReceive('generate')->andReturn('uuid-002');
    $repository->shouldReceive('save')->once();

    $publishedEvents = [];
    $publisher->shouldReceive('publish')->andReturnUsing(
        function ($event) use (&$publishedEvents) {
            $publishedEvents[] = $event;
        }
    );

    $handler = new CreateTradeHandler($repository, $publisher, $uuidGenerator);
    $handler->handle(new CreateTradeCommand(
        userId: 'user-001',
        assetSymbol: 'VALE3',
        market: 'B3',
        direction: 'SHORT',
        timeframe: 'H4',
    ));

    expect($publishedEvents)->toHaveCount(1)
        ->and($publishedEvents[0])->toBeInstanceOf(TradeCreated::class)
        ->and($publishedEvents[0]->assetSymbol())->toBe('VALE3')
        ->and($publishedEvents[0]->direction())->toBe('SHORT');
});
