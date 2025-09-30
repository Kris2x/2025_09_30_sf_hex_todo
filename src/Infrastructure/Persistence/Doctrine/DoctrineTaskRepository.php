<?php

namespace App\Infrastructure\Persistence\Doctrine;
use App\Domain\Model\Task;
use App\Domain\Port\TaskRepositoryInterface;

class DoctrineTaskRepository implements TaskRepositoryInterface
{
//    public function __construct(
//        private EntityManagerInterface $entityManager
//    ) {
//    }

    public function save(Task $task): void
    {
        // TODO: Implement save() method.
    }

    public function findById(string $id): ?Task
    {
        // TODO: Implement findById() method.
    }

    public function findAll(): array
    {
        // TODO: Implement findAll() method.
    }
}
