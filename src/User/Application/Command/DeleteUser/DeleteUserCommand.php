<?php

namespace App\User\Application\Command\DeleteUser;

final readonly class DeleteUserCommand
{
    public function __construct(
        public string $email
    )
    {
    }
}
