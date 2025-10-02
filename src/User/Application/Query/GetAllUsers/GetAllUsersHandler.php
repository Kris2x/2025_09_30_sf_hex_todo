<?php

namespace App\User\Application\Query\GetAllUsers;

use App\User\Domain\Port\UserRepositoryInterface;

final readonly class GetAllUsersHandler
{
    public function __construct(private UserRepositoryInterface $userRepository)
    {
    }

    public function handle(GetAllUsersQuery $query): array
    {
        return $this->userRepository->findAll();
    }
}
