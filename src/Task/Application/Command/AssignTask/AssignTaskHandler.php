<?php

namespace App\Task\Application\Command\AssignTask;

use App\Task\Domain\Port\CurrentUserProviderInterface;
use App\Task\Domain\Port\TaskRepositoryInterface;
use App\Task\Domain\Service\TaskAuthorizationService;
use App\User\Domain\Port\UserRepositoryInterface;
use DomainException;
use RuntimeException;

final readonly class AssignTaskHandler
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
        private UserRepositoryInterface $userRepository,
        private TaskAuthorizationService $authorizationService,
        private CurrentUserProviderInterface $currentUserProvider,
    )
    {
    }

    public function handle(AssignTaskCommand $command): void
    {
        $currentUser = $this->currentUserProvider->getCurrentUser();

        if ($currentUser === null || !$this->authorizationService->canAssign($currentUser)) {
            throw new RuntimeException('Access denied. Only administrators can assign tasks.');
        }

        $task = $this->taskRepository->findById($command->taskId);
        if ($task === null) {
            throw new DomainException(sprintf('Task with id "%s" not found', $command->taskId));
        }

        if ($command->userId === null) {
            $task->unassign();
        } else {
            $user = $this->userRepository->findById($command->userId);
            if ($user === null) {
                throw new DomainException(sprintf('User with id "%s" not found', $command->userId));
            }
            $task->assignTo($user);
        }

        $this->taskRepository->save($task);
    }
}
