<?php

declare(strict_types=1);

namespace App\Domain\Journal\ValueObjects;

enum ResultType: string
{
    case GAIN = 'GAIN';
    case LOSS = 'LOSS';
    case BREAKEVEN = 'BREAKEVEN';
}
