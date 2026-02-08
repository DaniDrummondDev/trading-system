<?php

declare(strict_types=1);

use App\Domain\Journal\ValueObjects\Money;
use App\Domain\Journal\ValueObjects\ResultType;
use App\Domain\Journal\ValueObjects\TradeOutcome;

it('cria outcome de ganho', function () {
    $outcome = new TradeOutcome(
        new Money('1500.00'),
        new Money('1450.00'),
        ResultType::GAIN,
        '2.5',
    );

    expect($outcome->grossResult()->amount())->toBe('1500.00')
        ->and($outcome->netResult()->amount())->toBe('1450.00')
        ->and($outcome->resultType())->toBe(ResultType::GAIN)
        ->and($outcome->realizedRR())->toBe('2.5')
        ->and($outcome->isLoss())->toBeFalse();
});

it('cria outcome de perda', function () {
    $outcome = new TradeOutcome(
        new Money('-500.00'),
        new Money('-520.00'),
        ResultType::LOSS,
        '-1.0',
    );

    expect($outcome->isLoss())->toBeTrue()
        ->and($outcome->resultType())->toBe(ResultType::LOSS);
});

it('cria outcome breakeven', function () {
    $outcome = new TradeOutcome(
        new Money('0'),
        new Money('-20.00'),
        ResultType::BREAKEVEN,
        '0',
    );

    expect($outcome->resultType())->toBe(ResultType::BREAKEVEN);
});

it('rejeita realizedRR não numérico', function () {
    new TradeOutcome(
        new Money('100.00'),
        new Money('90.00'),
        ResultType::GAIN,
        'abc',
    );
})->throws(\InvalidArgumentException::class, 'Realized R:R must be numeric.');

it('verifica igualdade', function () {
    $a = new TradeOutcome(new Money('100.00'), new Money('90.00'), ResultType::GAIN, '2.0');
    $b = new TradeOutcome(new Money('100.00'), new Money('90.00'), ResultType::GAIN, '2.0');
    $c = new TradeOutcome(new Money('200.00'), new Money('190.00'), ResultType::GAIN, '3.0');

    expect($a->equals($b))->toBeTrue()
        ->and($a->equals($c))->toBeFalse();
});
