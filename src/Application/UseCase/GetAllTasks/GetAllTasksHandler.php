<?php

namespace App\Application\UseCase\GetAllTasks;

use App\Domain\Model\Task;
use App\Domain\Port\TaskRepositoryInterface;

final readonly class GetAllTasksHandler
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository
    )
    {
    }

    /**
     * @return array<Task>
     */
    public function handle(GetAllTasksQuery $query): array
    {
        return $this->taskRepository->findAll();
    }
}
