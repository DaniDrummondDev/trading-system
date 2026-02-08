<?php

declare(strict_types=1);

use App\Domain\Trade\ValueObjects\Asset;

it('cria asset com symbol uppercase', function () {
    $asset = new Asset('petr4', 'B3');

    expect($asset->symbol())->toBe('PETR4')
        ->and($asset->market())->toBe('B3');
});

it('rejeita symbol vazio', function () {
    new Asset('', 'B3');
})->throws(\InvalidArgumentException::class, 'Asset symbol cannot be empty.');

it('rejeita market vazio', function () {
    new Asset('PETR4', '');
})->throws(\InvalidArgumentException::class, 'Asset market cannot be empty.');

it('verifica igualdade', function () {
    $a = new Asset('PETR4', 'B3');
    $b = new Asset('petr4', 'B3');
    $c = new Asset('VALE3', 'B3');

    expect($a->equals($b))->toBeTrue()
        ->and($a->equals($c))->toBeFalse();
});
