<?php

declare(strict_types=1);

use App\Domain\Journal\ValueObjects\Money;

it('cria money com amount e currency', function () {
    $money = new Money('1500.00', 'BRL');

    expect($money->amount())->toBe('1500.00')
        ->and($money->currency())->toBe('BRL');
});

it('usa BRL como moeda padrão', function () {
    $money = new Money('100.00');

    expect($money->currency())->toBe('BRL');
});

it('rejeita amount não numérico', function () {
    new Money('abc');
})->throws(\InvalidArgumentException::class, 'Money amount must be numeric.');

it('rejeita currency vazia', function () {
    new Money('100.00', '');
})->throws(\InvalidArgumentException::class, 'Currency cannot be empty.');

it('aceita valores negativos (perdas)', function () {
    $money = new Money('-500.00');

    expect($money->isNegative())->toBeTrue()
        ->and($money->isPositive())->toBeFalse();
});

it('identifica valor positivo', function () {
    $money = new Money('1500.00');

    expect($money->isPositive())->toBeTrue()
        ->and($money->isNegative())->toBeFalse();
});

it('identifica valor zero', function () {
    $money = new Money('0');

    expect($money->isZero())->toBeTrue()
        ->and($money->isPositive())->toBeFalse()
        ->and($money->isNegative())->toBeFalse();
});

it('soma dois valores da mesma moeda', function () {
    $a = new Money('1000.50');
    $b = new Money('500.25');

    $result = $a->add($b);

    expect($result->amount())->toBe('1500.75000000')
        ->and($result->currency())->toBe('BRL');
});

it('subtrai dois valores da mesma moeda', function () {
    $a = new Money('1000.00');
    $b = new Money('300.00');

    $result = $a->subtract($b);

    expect($result->amount())->toBe('700.00000000');
});

it('rejeita operação com moedas diferentes', function () {
    $brl = new Money('100.00', 'BRL');
    $usd = new Money('50.00', 'USD');

    $brl->add($usd);
})->throws(\InvalidArgumentException::class, 'Não é possível operar moedas diferentes');

it('verifica igualdade', function () {
    $a = new Money('1000.00', 'BRL');
    $b = new Money('1000.00', 'BRL');
    $c = new Money('2000.00', 'BRL');
    $d = new Money('1000.00', 'USD');

    expect($a->equals($b))->toBeTrue()
        ->and($a->equals($c))->toBeFalse()
        ->and($a->equals($d))->toBeFalse();
});
