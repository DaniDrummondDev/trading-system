<?php

declare(strict_types=1);

namespace App\Domain\Trade\ValueObjects;

enum PriceLevelType: string
{
    case ENTRY = 'ENTRY';
    case STOP = 'STOP';
    case TARGET = 'TARGET';
}
