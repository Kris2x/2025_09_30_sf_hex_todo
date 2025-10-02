<?php

namespace App\User\Domain\Port;

use App\User\Domain\Model\User;

interface PasswordHasherInterface
{
    public function hashPassword(User $user, string $plainPassword): string;
}
