<?php

namespace App\Task\Domain\Port;

use App\Task\Domain\Model\Task;

interface TaskRepositoryInterface
{
    public function save(Task $task): void;
    public function findById(string $id): ?Task;
    public function findAll(): array;
    public function delete(Task $task): void;
}
