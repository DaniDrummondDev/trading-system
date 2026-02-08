<?php

declare(strict_types=1);

namespace App\Application\Contracts;

use App\Application\Shared\DTOs\UserRiskProfileDTO;

interface UserRiskProfileProvider
{
    public function getByUserId(string $userId): UserRiskProfileDTO;
}
