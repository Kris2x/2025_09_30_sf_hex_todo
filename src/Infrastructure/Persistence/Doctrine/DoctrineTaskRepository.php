<?php

namespace App\Infrastructure\Persistence\Doctrine;

use App\Domain\Model\Task;
use App\Domain\Port\TaskRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineTaskRepository implements TaskRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {
    }

    public function save(Task $task): void
    {
        $this->entityManager->persist($task);
        $this->entityManager->flush();
    }

    public function findById(string $id): ?Task
    {
        return $this->entityManager->getRepository(Task::class)->find($id);
    }

    public function findAll(): array
    {
        return $this->entityManager->getRepository(Task::class)->findAll();
    }

    public function delete(Task $task): void
    {
        $this->entityManager->remove($task);
        $this->entityManager->flush();
    }
}
