<?php

declare(strict_types=1);

use App\Domain\Journal\ValueObjects\TradeLesson;

it('cria lesson com ambos campos', function () {
    $lesson = new TradeLesson(
        keepDoing: 'Respeitou o stop loss',
        improveNextTime: 'Esperar confirmação no candle de entrada',
    );

    expect($lesson->keepDoing())->toBe('Respeitou o stop loss')
        ->and($lesson->improveNextTime())->toBe('Esperar confirmação no candle de entrada');
});

it('rejeita keepDoing vazio', function () {
    new TradeLesson(keepDoing: '', improveNextTime: 'Algo');
})->throws(\InvalidArgumentException::class, 'Keep doing cannot be empty.');

it('rejeita improveNextTime vazio', function () {
    new TradeLesson(keepDoing: 'Algo', improveNextTime: '');
})->throws(\InvalidArgumentException::class, 'Improve next time cannot be empty.');

it('verifica igualdade', function () {
    $a = new TradeLesson('Manter', 'Melhorar');
    $b = new TradeLesson('Manter', 'Melhorar');
    $c = new TradeLesson('Manter', 'Outro');

    expect($a->equals($b))->toBeTrue()
        ->and($a->equals($c))->toBeFalse();
});
