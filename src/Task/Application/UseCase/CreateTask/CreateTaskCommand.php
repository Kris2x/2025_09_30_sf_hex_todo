<?php

namespace App\Task\Application\UseCase\CreateTask;

final readonly class CreateTaskCommand
{
    public function __construct(
        public string $title,
        public string $description,
    ){}
}
