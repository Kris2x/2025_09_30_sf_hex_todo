<?php

namespace App\Task\Application\Command\AssignTask;

use App\Task\Domain\Port\TaskRepositoryInterface;
use App\User\Domain\Port\UserRepositoryInterface;
use DomainException;

final readonly class AssignTaskHandler
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
        private UserRepositoryInterface $userRepository,
    )
    {
    }

    public function handle(AssignTaskCommand $command): void
    {
        $task = $this->taskRepository->findById($command->taskId);
        if ($task === null) {
            throw new DomainException(sprintf('Task with id "%s" not found', $command->taskId));
        }

        if ($command->userId === null) {
            $task->unassign();
        } else {
            $user = $this->userRepository->findById($command->userId);
            if ($user === null) {
                throw new DomainException(sprintf('User with id "%s" not found', $command->userId));
            }
            $task->assignTo($user);
        }

        $this->taskRepository->save($task);
    }
}
