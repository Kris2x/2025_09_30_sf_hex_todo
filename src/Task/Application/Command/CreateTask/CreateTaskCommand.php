<?php

namespace App\Task\Application\Command\CreateTask;

final readonly class CreateTaskCommand
{
    public function __construct(
        public string $title,
        public string $description,
    ){}
}
