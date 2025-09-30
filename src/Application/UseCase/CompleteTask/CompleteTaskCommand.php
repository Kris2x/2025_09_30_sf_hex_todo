<?php

namespace App\Application\UseCase\CompleteTask;

final readonly class CompleteTaskCommand
{
    public function __construct(
        public string $taskId,
    ) {}
}
