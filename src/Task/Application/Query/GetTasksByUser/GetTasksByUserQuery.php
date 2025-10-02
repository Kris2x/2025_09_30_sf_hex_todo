<?php

namespace App\Task\Application\Query\GetTasksByUser;

final readonly class GetTasksByUserQuery
{
    public function __construct(
        public string $userId,
    ){}
}
