<?php

declare(strict_types=1);

namespace App\Domain\Journal\ValueObjects;

enum EmotionalState: string
{
    case CONTROLLED = 'CONTROLLED';
    case ANXIOUS = 'ANXIOUS';
    case IMPULSIVE = 'IMPULSIVE';
    case OVERCONFIDENT = 'OVERCONFIDENT';

    public function isDivergent(): bool
    {
        return $this !== self::CONTROLLED;
    }
}
