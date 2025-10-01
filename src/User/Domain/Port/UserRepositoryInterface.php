<?php

namespace App\User\Domain\Port;

use App\User\Domain\Model\User;

interface UserRepositoryInterface
{
    public function save(User $user): void;

    public function findById(string $id): ?User;

    public function findByEmail(string $email): ?User;

    /** @return User[] */
    public function findAll(): array;

    public function remove(User $user): void;

}
