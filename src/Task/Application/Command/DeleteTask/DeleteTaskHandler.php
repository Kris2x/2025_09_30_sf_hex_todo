<?php

namespace App\Task\Application\Command\DeleteTask;

use App\Task\Domain\Port\CurrentUserProviderInterface;
use App\Task\Domain\Port\TaskRepositoryInterface;
use App\Task\Domain\Service\TaskAuthorizationService;
use InvalidArgumentException;
use RuntimeException;

final readonly class DeleteTaskHandler
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
        private TaskAuthorizationService $authorizationService,
        private CurrentUserProviderInterface $currentUserProvider,
    )
    {
    }

    public function handle(DeleteTaskCommand $command): void
    {
        $task = $this->taskRepository->findById($command->taskId);

        if ($task === null) {
            throw new InvalidArgumentException(sprintf('Task with ID "%s" not found.', $command->taskId));
        }

        $currentUser = $this->currentUserProvider->getCurrentUser();

        if ($currentUser === null || !$this->authorizationService->canDelete($task, $currentUser)) {
            throw new RuntimeException('Access denied. You are not authorized to delete this task.');
        }

        $this->taskRepository->delete($task);
    }
}
