<?php

namespace App\Application\CreateTask;

final readonly class CreateTaskCommand
{
    public function __construct(
        public string $title,
        public string $description,
    ){}
}
