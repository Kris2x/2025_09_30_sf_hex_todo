<?php

namespace App\Task\Application\Query\GetMyTasks;

use App\Task\Domain\Port\CurrentUserProviderInterface;
use App\Task\Domain\Port\TaskRepositoryInterface;

final readonly class GetMyTasksHandler
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
        private CurrentUserProviderInterface $currentUserProvider,
    ) {
    }

    /**
     * @return array
     */
    public function handle(GetMyTasksQuery $query): array
    {
        $currentUser = $this->currentUserProvider->getCurrentUser();

        if (!$currentUser) {
            return [];
        }

        return $this->taskRepository->findByUser($currentUser);
    }
}
