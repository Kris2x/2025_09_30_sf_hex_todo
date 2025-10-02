<?php

namespace App\Task\Application\Command\DeleteTask;

final readonly class DeleteTaskCommand
{
    public function __construct(
        public string $taskId,
    ) {}
}
