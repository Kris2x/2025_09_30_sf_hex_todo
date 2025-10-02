<?php

namespace App\Task\Infrastructure\Security;

use App\Task\Domain\Model\Task;
use App\Task\Domain\Service\TaskAuthorizationService;
use App\User\Domain\Model\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class TaskVoter extends Voter
{
    public const EDIT = 'TASK_EDIT';
    public const DELETE = 'TASK_DELETE';
    public const COMPLETE = 'TASK_COMPLETE';

    public function __construct(
        private readonly TaskAuthorizationService $authorizationService
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::EDIT, self::DELETE, self::COMPLETE])) {
            return false;
        }

        if (!$subject instanceof Task) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Task $task */
        $task = $subject;

        // Voter deleguje decyzje do Domain Service (logika biznesowa)
        return match ($attribute) {
            self::EDIT => $this->authorizationService->canEdit($task, $user),
            self::DELETE => $this->authorizationService->canDelete($task, $user),
            self::COMPLETE => $this->authorizationService->canComplete($task, $user),
            default => false,
        };
    }
}
