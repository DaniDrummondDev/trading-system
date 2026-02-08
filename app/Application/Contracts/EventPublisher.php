<?php

declare(strict_types=1);

namespace App\Application\Contracts;

use App\Domain\Shared\Events\DomainEvent;

interface EventPublisher
{
    public function publish(DomainEvent $event): void;
}
