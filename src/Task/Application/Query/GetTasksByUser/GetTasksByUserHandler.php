<?php

namespace App\Task\Application\Query\GetTasksByUser;

use App\Task\Domain\Model\Task;
use App\Task\Domain\Port\TaskRepositoryInterface;
use App\User\Domain\Port\UserRepositoryInterface;
use DomainException;

final readonly class GetTasksByUserHandler
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
        private UserRepositoryInterface $userRepository,
    )
    {
    }

    /** @return Task[] */
    public function handle(GetTasksByUserQuery $query): array
    {
        $user = $this->userRepository->findById($query->userId);
        if ($user === null) {
            throw new DomainException(sprintf('User with id "%s" not found', $query->userId));
        }

        return $this->taskRepository->findByAssignee($user);
    }
}
