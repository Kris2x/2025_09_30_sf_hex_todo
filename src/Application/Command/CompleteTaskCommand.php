<?php

namespace App\Application\Command;

final readonly class CompleteTaskCommand
{
    public function __construct(
        public string $taskId,
    ) {}
}
