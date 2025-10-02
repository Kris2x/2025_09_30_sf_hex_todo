<?php

namespace App\Task\Domain\Port;

use App\User\Domain\Model\User;

interface CurrentUserProviderInterface
{
    public function getCurrentUser(): ?User;
}
