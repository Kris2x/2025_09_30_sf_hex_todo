<?php

namespace App\Task\Infrastructure\Security;

use App\Task\Domain\Port\CurrentUserProviderInterface;
use App\User\Domain\Model\User;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class SymfonyCurrentUserProvider implements CurrentUserProviderInterface
{
    public function __construct(
        private Security $security
    )
    {
    }

    public function getCurrentUser(): ?User
    {
        $user = $this->security->getUser();

        return $user instanceof User ? $user : null;
    }
}
