<?php

namespace App\Task\Application\Query\GetAllTasks;

use App\Task\Domain\Model\Task;
use App\Task\Domain\Port\TaskRepositoryInterface;

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
