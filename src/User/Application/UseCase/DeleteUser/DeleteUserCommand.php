<?php

namespace App\User\Application\UseCase\DeleteUser;

class DeleteUserCommand
{
    public function __construct(
        public string $email
    )
    {
    }
}
