<?php

namespace App\User\Application\UseCase\DeleteUser;

use App\User\Domain\Port\UserRepositoryInterface;

final readonly  class DeleteUserHandler
{
    public function __construct(private UserRepositoryInterface $repository)
    {
    }

    public function handle(DeleteUserCommand $command): void
    {
        $user = $this->repository->findByEmail($command->email);
        if ($user === null) {
            throw new \InvalidArgumentException('User not found');
        }
        $this->repository->remove($user);
    }
}
