<?php

namespace App\Task\Application\UseCase\DeleteTask;

final readonly class DeleteTaskCommand
{
    public function __construct(
        public string $taskId,
    ) {}
}
