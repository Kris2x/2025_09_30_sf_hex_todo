<?php

namespace App\Application\CompleteTask;

use App\Domain\Model\Task;
use App\Domain\Port\TaskRepositoryInterface;
use InvalidArgumentException;

readonly class CompleteTaskCommandHandler
{
    public function __construct(private TaskRepositoryInterface $taskRepository)
    {
    }

    public function handle(CompleteTaskCommand $command): Task
    {
        $taskId = $command->getTaskId();
        $task = $this->taskRepository->findById($taskId);

        if ($task === null) {
            throw new InvalidArgumentException(sprintf('Task with ID "%s" not found.', $taskId));
        }

        $task->complete();
        $this->taskRepository->save($task);

        return $task;
    }
}
