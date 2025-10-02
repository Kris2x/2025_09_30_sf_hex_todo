<?php

namespace App\Task\Infrastructure\Persistence\Doctrine;

use App\Task\Domain\Model\Task;
use App\Task\Domain\Port\TaskRepositoryInterface;
use App\User\Domain\Model\User;
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

    public function findByAssignee(User $user): array
    {
        return $this->entityManager->getRepository(Task::class)->findBy(['assignee' => $user]);
    }

    public function findByUser(User $user): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('t')
            ->from(Task::class, 't')
            ->where('t.assignee = :user OR t.createdBy = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
}
