<?php

declare(strict_types=1);

namespace App\Domain\Trade\ValueObjects;

enum Timeframe: string
{
    case D1 = 'D1';
    case H4 = 'H4';
    case H1 = 'H1';
    case M15 = 'M15';
}
