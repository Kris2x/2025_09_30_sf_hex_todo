<?php

namespace App\Task\Application\Command\DeleteTask;

use App\Task\Domain\Port\AuthorizationInterface;
use App\Task\Domain\Port\TaskRepositoryInterface;
use InvalidArgumentException;
use RuntimeException;

final readonly class DeleteTaskHandler
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
        private AuthorizationInterface $authorization
    )
    {
    }

    public function handle(DeleteTaskCommand $command): void
    {
        $task = $this->taskRepository->findById($command->taskId);

        if ($task === null) {
            throw new InvalidArgumentException(sprintf('Task with ID "%s" not found.', $command->taskId));
        }

        if (!$this->authorization->canDeleteTask($task)) {
            throw new RuntimeException('Access denied. You are not authorized to delete this task.');
        }

        $this->taskRepository->delete($task);
    }
}
