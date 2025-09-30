<?php

namespace App\Application\Command;

final readonly class DeleteTaskCommand
{
    public function __construct(
        public string $taskId,
    ) {}
}