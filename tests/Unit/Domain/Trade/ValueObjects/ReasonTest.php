<?php

declare(strict_types=1);

use App\Domain\Trade\ValueObjects\Reason;

it('cria reason com code e description', function () {
    $reason = new Reason('RISK_EXCEEDED', 'Risco máximo por trade excedido');

    expect($reason->code())->toBe('RISK_EXCEEDED')
        ->and($reason->description())->toBe('Risco máximo por trade excedido');
});

it('rejeita code vazio', function () {
    new Reason('', 'Descrição');
})->throws(\InvalidArgumentException::class, 'Reason code cannot be empty.');

it('rejeita description vazio', function () {
    new Reason('CODE', '');
})->throws(\InvalidArgumentException::class, 'Reason description cannot be empty.');

it('verifica igualdade', function () {
    $a = new Reason('RISK', 'Excedido');
    $b = new Reason('RISK', 'Excedido');
    $c = new Reason('RISK', 'Outro motivo');

    expect($a->equals($b))->toBeTrue()
        ->and($a->equals($c))->toBeFalse();
});
