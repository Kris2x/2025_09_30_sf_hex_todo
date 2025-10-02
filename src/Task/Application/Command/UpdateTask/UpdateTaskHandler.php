<?php

namespace App\Task\Application\Command\UpdateTask;

use App\Task\Domain\Port\CurrentUserProviderInterface;
use App\Task\Domain\Port\TaskRepositoryInterface;
use App\Task\Domain\Service\TaskAuthorizationService;
use App\User\Domain\Port\UserRepositoryInterface;
use DomainException;
use InvalidArgumentException;
use RuntimeException;

final readonly class UpdateTaskHandler
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
        private TaskAuthorizationService $authorizationService,
        private UserRepositoryInterface $userRepository,
        private CurrentUserProviderInterface $currentUserProvider,
    )
    {
    }

    public function handle(UpdateTaskCommand $command): void
    {
        $task = $this->taskRepository->findById($command->taskId);

        if ($task === null) {
            throw new InvalidArgumentException('Task not found');
        }

        $currentUser = $this->currentUserProvider->getCurrentUser();

        if ($currentUser === null || !$this->authorizationService->canEdit($task, $currentUser)) {
            throw new RuntimeException('Access denied. You are not authorized to edit this task.');
        }

        $task->setTitle($command->title);
        $task->setDescription($command->description);

        if ($command->assigneeId !== null) {
            $user = $this->userRepository->findById($command->assigneeId);
            if ($user === null) {
                throw new DomainException(sprintf('User with id "%s" not found', $command->assigneeId));
            }
            $task->assignTo($user);
        }

        $this->taskRepository->save($task);
    }
}
