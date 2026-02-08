<?php

declare(strict_types=1);

use App\Domain\Trade\ValueObjects\Price;

it('cria price com valor positivo', function () {
    $price = new Price('100.50');

    expect($price->amount())->toBe('100.50');
});

it('rejeita price zero', function () {
    new Price('0');
})->throws(\InvalidArgumentException::class, 'Price must be positive.');

it('rejeita price negativo', function () {
    new Price('-10.00');
})->throws(\InvalidArgumentException::class, 'Price must be positive.');

it('rejeita valor não numérico', function () {
    new Price('abc');
})->throws(\InvalidArgumentException::class, 'Price amount must be numeric.');

it('compara maior que', function () {
    $higher = new Price('100.00');
    $lower = new Price('50.00');

    expect($higher->isGreaterThan($lower))->toBeTrue()
        ->and($lower->isGreaterThan($higher))->toBeFalse();
});

it('compara menor que', function () {
    $higher = new Price('100.00');
    $lower = new Price('50.00');

    expect($lower->isLessThan($higher))->toBeTrue()
        ->and($higher->isLessThan($lower))->toBeFalse();
});

it('soma dois prices', function () {
    $a = new Price('100.50');
    $b = new Price('50.25');
    $result = $a->add($b);

    expect($result->amount())->toBe('150.75000000');
});

it('subtrai dois prices', function () {
    $a = new Price('100.00');
    $b = new Price('30.00');
    $result = $a->subtract($b);

    expect($result->amount())->toBe('70.00000000');
});

it('rejeita subtração que resulta em zero ou negativo', function () {
    $a = new Price('50.00');
    $b = new Price('100.00');

    $a->subtract($b);
})->throws(\InvalidArgumentException::class, 'Subtraction would result in non-positive price.');

it('verifica igualdade', function () {
    $a = new Price('100.00');
    $b = new Price('100.00');
    $c = new Price('200.00');

    expect($a->equals($b))->toBeTrue()
        ->and($a->equals($c))->toBeFalse();
});
