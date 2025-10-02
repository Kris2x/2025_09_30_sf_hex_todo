<?php

namespace App\User\Application\Query;

use App\User\Domain\Model\User;
use App\User\Domain\Port\UserRepositoryInterface;

final readonly class UserQueryService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {
    }

    /**
     * @return User[]
     */
    public function getAllUsers(): array
    {
        return $this->userRepository->findAll();
    }

    public function findById(string $id): ?User
    {
        return $this->userRepository->findById($id);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->userRepository->findByEmail($email);
    }
}
