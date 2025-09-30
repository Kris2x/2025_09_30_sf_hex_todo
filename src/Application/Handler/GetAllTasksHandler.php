<?php

namespace App\Application\Handler;

use App\Application\Query\GetAllTasksQuery;
use App\Domain\Model\Task;
use App\Domain\Port\TaskRepositoryInterface;

readonly class GetAllTasksHandler
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
