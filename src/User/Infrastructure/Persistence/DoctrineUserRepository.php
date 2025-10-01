<?php

namespace App\User\Infrastructure\Persistence;

use App\User\Domain\Model\User;
use App\User\Domain\Port\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineUserRepository implements UserRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    )
    {
    }

    public function save(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function findById(string $id): ?User
    {
        return $this->entityManager->getRepository(User::class)->find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
    }

    public function findAll(): array
    {
        return $this->entityManager->getRepository(User::class)->findAll();
    }

    public function remove(User $user): void
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }
}
