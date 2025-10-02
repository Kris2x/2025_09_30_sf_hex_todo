<?php

namespace App\Task\Infrastructure\Security;

use App\Task\Domain\Model\Task;
use App\Task\Domain\Port\AuthorizationInterface;
use App\User\Domain\Model\User;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class SymfonyAuthorizationAdapter implements AuthorizationInterface
{
    public function __construct(
        private Security $security
    )
    {
    }

    public function canEditTask(Task $task): bool
    {
        return $this->security->isGranted(TaskVoter::EDIT, $task);
    }

    public function canDeleteTask(Task $task): bool
    {
        return $this->security->isGranted(TaskVoter::DELETE, $task);
    }

    public function canCompleteTask(Task $task): bool
    {
        return $this->security->isGranted(TaskVoter::COMPLETE, $task);
    }

    public function canAssignTask(): bool
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return false;
        }

        return in_array('ROLE_ADMIN', $user->getRoles(), true);
    }
}
