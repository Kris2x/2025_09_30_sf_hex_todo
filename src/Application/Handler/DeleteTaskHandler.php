<?php

namespace App\Application\Handler;

use App\Application\Command\DeleteTaskCommand;
use App\Domain\Port\TaskRepositoryInterface;
use InvalidArgumentException;

readonly class DeleteTaskHandler
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository
    )
    {
    }

    public function handle(DeleteTaskCommand $command): void
    {
        $taskId = $command->getTaskId();
        $task = $this->taskRepository->findById($taskId);

        if ($task === null) {
            throw new InvalidArgumentException(sprintf('Task with ID "%s" not found.', $taskId));
        }

        $this->taskRepository->delete($task);
    }
}