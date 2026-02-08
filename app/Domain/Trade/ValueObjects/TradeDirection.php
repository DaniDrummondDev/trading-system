<?php

declare(strict_types=1);

namespace App\Domain\Trade\ValueObjects;

enum TradeDirection: string
{
    case LONG = 'LONG';
    case SHORT = 'SHORT';
}
