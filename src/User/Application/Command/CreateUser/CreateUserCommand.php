<?php

namespace App\User\Application\Command\CreateUser;

final readonly class CreateUserCommand
{
    public function __construct(
        public string $email,
        public string $firstName,
        public string $lastName
    )
    {
    }
}
