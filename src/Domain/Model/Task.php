<?php

namespace App\Domain\Model;

use DomainException;

class Task
{
    public function __construct(
        private string $id,
        private string $title,
        private string $description,
        private bool $isCompleted = false,
    )
    {
    }

    public function complete(): void
    {
        if ($this->isCompleted) {
            throw new DomainException('Task is already completed');
        }
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function isCompleted(): bool
    {
        return $this->isCompleted;
    }
}
