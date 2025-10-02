<?php

namespace App\Task\Application\Command\CompleteTask;

final readonly class CompleteTaskCommand
{
    public function __construct(
        public string $taskId,
    ) {}
}
