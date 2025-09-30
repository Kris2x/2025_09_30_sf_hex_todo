<?php

namespace App\Application\UseCase\CompleteTask;

use App\Domain\Model\Task;
use App\Domain\Port\TaskRepositoryInterface;
use InvalidArgumentException;

final readonly class CompleteTaskHandler
{
    public function __construct(private TaskRepositoryInterface $taskRepository)
    {
    }

    public function handle(CompleteTaskCommand $command): Task
    {
        $task = $this->taskRepository->findById($command->taskId);

        if ($task === null) {
            throw new InvalidArgumentException(sprintf('Task with ID "%s" not found.', $command->taskId));
        }

        $task->complete();
        $this->taskRepository->save($task);

        return $task;
    }
}
