<?php

namespace App\Task\Application\Command\UpdateTask;

class UpdateTaskCommand
{
    public function __construct(
        public string $taskId,
        public string $title,
        public string $description,
        public ?string $assigneeId = null,
    ){}
}
