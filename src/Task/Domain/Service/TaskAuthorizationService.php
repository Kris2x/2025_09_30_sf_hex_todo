<?php

namespace App\Task\Domain\Service;

use App\Task\Domain\Model\Task;
use App\User\Domain\Model\User;

/**
 * Domain Service odpowiedzialny za reguły autoryzacji związane z taskami.
 *
 * Zawiera logikę biznesową określającą kto i co może robić z taskami.
 */
final readonly class TaskAuthorizationService
{
    /**
     * Sprawdza czy użytkownik może edytować task.
     *
     * Reguły:
     * - Admin może edytować wszystkie taski
     * - Właściciel (createdBy) może edytować swój task
     * - Assignee może edytować przypisany do niego task
     */
    public function canEdit(Task $task, User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($task->isOwnedBy($user)) {
            return true;
        }

        if ($task->isAssignedTo($user)) {
            return true;
        }

        return false;
    }

    /**
     * Sprawdza czy użytkownik może usunąć task.
     *
     * Reguły:
     * - Admin może usuwać wszystkie taski
     * - Tylko właściciel (createdBy) może usunąć swój task
     */
    public function canDelete(Task $task, User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $task->isOwnedBy($user);
    }

    /**
     * Sprawdza czy użytkownik może oznaczyć task jako ukończony.
     *
     * Reguły:
     * - Admin może ukończyć wszystkie taski
     * - Właściciel (createdBy) może ukończyć swój task
     * - Assignee może ukończyć przypisany do niego task
     */
    public function canComplete(Task $task, User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($task->isOwnedBy($user)) {
            return true;
        }

        if ($task->isAssignedTo($user)) {
            return true;
        }

        return false;
    }

    /**
     * Sprawdza czy użytkownik może przypisywać taski.
     *
     * Reguły:
     * - Tylko administratorzy mogą przypisywać taski
     */
    public function canAssign(User $user): bool
    {
        return $user->isAdmin();
    }
}
