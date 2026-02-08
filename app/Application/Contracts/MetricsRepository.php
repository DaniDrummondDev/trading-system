<?php

declare(strict_types=1);

namespace App\Application\Contracts;

use App\Domain\Metrics\Entities\TraderMetrics;

interface MetricsRepository
{
    public function update(TraderMetrics $metrics): void;

    public function getCurrent(): TraderMetrics;
}
