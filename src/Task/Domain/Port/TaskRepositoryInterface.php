<?php

namespace App\Task\Domain\Port;

use App\Task\Domain\Model\Task;
use App\User\Domain\Model\User;

interface TaskRepositoryInterface
{
    public function save(Task $task): void;
    public function findById(string $id): ?Task;
    public function findAll(): array;
    public function delete(Task $task): void;

    /** @return Task[] */
    public function findByAssignee(User $user): array;

    /** @return Task[] - Tasks where user is assignee OR createdBy */
    public function findByUser(User $user): array;
}
