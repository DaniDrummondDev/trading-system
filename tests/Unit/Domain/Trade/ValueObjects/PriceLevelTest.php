<?php

declare(strict_types=1);

use App\Domain\Trade\ValueObjects\Price;
use App\Domain\Trade\ValueObjects\PriceLevel;
use App\Domain\Trade\ValueObjects\PriceLevelType;

it('cria price level com price e tipo', function () {
    $price = new Price('100.00');
    $level = new PriceLevel($price, PriceLevelType::ENTRY);

    expect($level->price())->toBe($price)
        ->and($level->type())->toBe(PriceLevelType::ENTRY);
});

it('verifica igualdade de price levels', function () {
    $a = new PriceLevel(new Price('100.00'), PriceLevelType::ENTRY);
    $b = new PriceLevel(new Price('100.00'), PriceLevelType::ENTRY);
    $c = new PriceLevel(new Price('100.00'), PriceLevelType::STOP);
    $d = new PriceLevel(new Price('200.00'), PriceLevelType::ENTRY);

    expect($a->equals($b))->toBeTrue()
        ->and($a->equals($c))->toBeFalse()
        ->and($a->equals($d))->toBeFalse();
});
