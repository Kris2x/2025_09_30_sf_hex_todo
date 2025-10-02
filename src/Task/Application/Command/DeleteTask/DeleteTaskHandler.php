<?php

namespace App\Task\Application\Command\DeleteTask;

use App\Task\Domain\Port\TaskRepositoryInterface;
use InvalidArgumentException;

final readonly class DeleteTaskHandler
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository
    )
    {
    }

    public function handle(DeleteTaskCommand $command): void
    {
        $task = $this->taskRepository->findById($command->taskId);

        if ($task === null) {
            throw new InvalidArgumentException(sprintf('Task with ID "%s" not found.', $command->taskId));
        }

        $this->taskRepository->delete($task);
    }
}
