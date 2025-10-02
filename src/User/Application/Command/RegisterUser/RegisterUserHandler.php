<?php

namespace App\User\Application\Command\RegisterUser;

use App\User\Domain\Model\User;
use App\User\Domain\Port\PasswordHasherInterface;
use App\User\Domain\Port\UserRepositoryInterface;

final readonly class RegisterUserHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordHasherInterface $passwordHasher
    )
    {
    }

    public function handle(RegisterUserCommand $command): User
    {
        $user = new User(
            $command->email,
            $command->firstName,
            $command->lastName
        );

        $hashedPassword = $this->passwordHasher->hashPassword($user, $command->password);
        $user->setPassword($hashedPassword);

        $this->userRepository->save($user);

        return $user;
    }
}
