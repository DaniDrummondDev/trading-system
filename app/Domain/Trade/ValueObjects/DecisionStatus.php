<?php

declare(strict_types=1);

namespace App\Domain\Trade\ValueObjects;

enum DecisionStatus: string
{
    case ALLOW = 'ALLOW';
    case BLOCK = 'BLOCK';
    case WAIT = 'WAIT';
}
