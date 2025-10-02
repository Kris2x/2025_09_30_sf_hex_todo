<?php

namespace App\Task\Infrastructure\Security;

use App\Task\Domain\Model\Task;
use App\User\Domain\Model\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class TaskVoter extends Voter
{
    public const EDIT = 'TASK_EDIT';
    public const DELETE = 'TASK_DELETE';
    public const COMPLETE = 'TASK_COMPLETE';

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

        return match ($attribute) {
            self::EDIT => $this->canEdit($task, $user),
            self::DELETE => $this->canDelete($task, $user),
            self::COMPLETE => $this->canComplete($task, $user),
            default => false,
        };
    }

    private function canEdit(Task $task, User $user): bool
    {
        // Admin może wszystko
        if ($this->isAdmin($user)) {
            return true;
        }

        // Właściciel (createdBy) może edytować
        if ($task->isOwnedBy($user)) {
            return true;
        }

        // Assignee może edytować
        if ($task->getAssignee() !== null && $task->getAssignee()->getId() === $user->getId()) {
            return true;
        }

        return false;
    }

    private function canDelete(Task $task, User $user): bool
    {
        // Admin może wszystko
        if ($this->isAdmin($user)) {
            return true;
        }

        // Tylko właściciel może usuwać
        return $task->isOwnedBy($user);
    }

    private function canComplete(Task $task, User $user): bool
    {
        // Admin może wszystko
        if ($this->isAdmin($user)) {
            return true;
        }

        // Właściciel (createdBy) może ukończyć
        if ($task->isOwnedBy($user)) {
            return true;
        }

        // Assignee może ukończyć
        if ($task->getAssignee() !== null && $task->getAssignee()->getId() === $user->getId()) {
            return true;
        }

        return false;
    }

    private function isAdmin(User $user): bool
    {
        return in_array('ROLE_ADMIN', $user->getRoles(), true);
    }
}
