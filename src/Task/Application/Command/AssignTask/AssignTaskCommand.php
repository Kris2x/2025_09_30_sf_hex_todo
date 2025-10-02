<?php

namespace App\Task\Application\Command\AssignTask;

final readonly class AssignTaskCommand
{
    public function __construct(
        public string $taskId,
        public ?string $userId,
    ){}
}
