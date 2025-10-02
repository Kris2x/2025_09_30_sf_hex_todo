<?php

namespace App\User\Application\UseCase\RegisterUser;

final readonly class RegisterUserCommand
{
    public function __construct(
        public string $email,
        public string $firstName,
        public string $lastName,
        public string $password
    )
    {
    }
}
