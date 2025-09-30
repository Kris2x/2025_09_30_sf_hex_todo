<?php

namespace App\Application\UseCase\CreateTask;

use App\Domain\Model\Task;
use App\Domain\Port\TaskRepositoryInterface;

final readonly class CreateTaskHandler
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository
    )
    {
    }

    public function handle(CreateTaskCommand $command): Task
    {
        $task = new Task(
            title: $command->title,
            description: $command->description
        );

        $this->taskRepository->save($task);

        return $task;
    }
}
