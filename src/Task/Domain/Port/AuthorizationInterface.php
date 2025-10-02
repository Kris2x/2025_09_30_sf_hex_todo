<?php

namespace App\Task\Domain\Port;

use App\Task\Domain\Model\Task;

interface AuthorizationInterface
{
    public function canEditTask(Task $task): bool;

    public function canDeleteTask(Task $task): bool;

    public function canCompleteTask(Task $task): bool;

    public function canAssignTask(): bool;
}
