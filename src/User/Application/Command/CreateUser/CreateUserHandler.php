<?php

namespace App\User\Application\Command\CreateUser;

use App\User\Domain\Model\User;
use App\User\Domain\Port\UserRepositoryInterface;

final readonly class CreateUserHandler
{
    public function __construct(private UserRepositoryInterface $userRepository)
    {
    }

    public function handle(CreateUserCommand $command): User
    {
        $user = new User(
            $command->email,
            $command->firstName,
            $command->lastName
        );

        $this->userRepository->save($user);

        return $user;
    }
}
