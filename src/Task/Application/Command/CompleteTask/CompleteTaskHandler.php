<?php

namespace App\Task\Application\Command\CompleteTask;

use App\Task\Domain\Model\Task;
use App\Task\Domain\Port\CurrentUserProviderInterface;
use App\Task\Domain\Port\TaskRepositoryInterface;
use App\Task\Domain\Service\TaskAuthorizationService;
use InvalidArgumentException;
use RuntimeException;

final readonly class CompleteTaskHandler
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
        private TaskAuthorizationService $authorizationService,
        private CurrentUserProviderInterface $currentUserProvider,
    )
    {
    }

    public function handle(CompleteTaskCommand $command): Task
    {
        $task = $this->taskRepository->findById($command->taskId);

        if ($task === null) {
            throw new InvalidArgumentException(sprintf('Task with ID "%s" not found.', $command->taskId));
        }

        $currentUser = $this->currentUserProvider->getCurrentUser();

        if ($currentUser === null || !$this->authorizationService->canComplete($task, $currentUser)) {
            throw new RuntimeException('Access denied. You are not authorized to complete this task.');
        }

        $task->complete();
        $this->taskRepository->save($task);

        return $task;
    }
}
