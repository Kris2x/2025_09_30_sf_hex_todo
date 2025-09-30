<?php

namespace App\Application\Command;

final readonly class CreateTaskCommand
{
    public function __construct(
        public string $title,
        public string $description,
    ){}
}
