<?php

namespace App\Application\Command;

final readonly class CompleteTaskCommand
{
    public function __construct(
        private string $taskId,
    )
    {
    }

    public function getTaskId(): string
    {
        return $this->taskId;
    }
}
