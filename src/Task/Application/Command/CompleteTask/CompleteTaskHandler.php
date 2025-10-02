<?php

namespace App\Task\Application\Command\CompleteTask;

use App\Task\Domain\Model\Task;
use App\Task\Domain\Port\AuthorizationInterface;
use App\Task\Domain\Port\TaskRepositoryInterface;
use InvalidArgumentException;
use RuntimeException;

final readonly class CompleteTaskHandler
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
        private AuthorizationInterface $authorization
    )
    {
    }

    public function handle(CompleteTaskCommand $command): Task
    {
        $task = $this->taskRepository->findById($command->taskId);

        if ($task === null) {
            throw new InvalidArgumentException(sprintf('Task with ID "%s" not found.', $command->taskId));
        }

        if (!$this->authorization->canCompleteTask($task)) {
            throw new RuntimeException('Access denied. You are not authorized to complete this task.');
        }

        $task->complete();
        $this->taskRepository->save($task);

        return $task;
    }
}
