<?php

declare(strict_types=1);

namespace App\Domain\Trade\ValueObjects;

enum TradeState: string
{
    case CREATED = 'CREATED';
    case ANALYZED = 'ANALYZED';
    case RISK_VALIDATED = 'RISK_VALIDATED';
    case APPROVED = 'APPROVED';
    case EXECUTED = 'EXECUTED';
    case CLOSED = 'CLOSED';
    case BLOCKED = 'BLOCKED';
    case EXPIRED = 'EXPIRED';

    /**
     * @return TradeState[]
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::CREATED => [self::ANALYZED],
            self::ANALYZED => [self::RISK_VALIDATED, self::BLOCKED],
            self::RISK_VALIDATED => [self::APPROVED, self::BLOCKED],
            self::APPROVED => [self::EXECUTED, self::EXPIRED],
            self::EXECUTED => [self::CLOSED],
            self::CLOSED, self::BLOCKED, self::EXPIRED => [],
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return in_array($target, $this->allowedTransitions(), true);
    }

    public function transitionTo(self $target): self
    {
        if (! $this->canTransitionTo($target)) {
            throw new \DomainException(
                "Transição inválida: {$this->value} → {$target->value}"
            );
        }

        return $target;
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::CLOSED, self::BLOCKED, self::EXPIRED], true);
    }
}
