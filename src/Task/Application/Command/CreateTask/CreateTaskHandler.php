<?php

namespace App\Task\Application\Command\CreateTask;

use App\Task\Domain\Model\Task;
use App\Task\Domain\Port\TaskRepositoryInterface;
use App\User\Domain\Port\UserRepositoryInterface;
use DomainException;

final readonly class CreateTaskHandler
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
        private UserRepositoryInterface $userRepository,
    )
    {
    }

    public function handle(CreateTaskCommand $command): Task
    {
        $task = new Task(
            title: $command->title,
            description: $command->description
        );

        if ($command->assigneeId !== null) {
            $user = $this->userRepository->findById($command->assigneeId);
            if ($user === null) {
                throw new DomainException(sprintf('User with id "%s" not found', $command->assigneeId));
            }
            $task->assignTo($user);
        }

        $this->taskRepository->save($task);

        return $task;
    }
}
