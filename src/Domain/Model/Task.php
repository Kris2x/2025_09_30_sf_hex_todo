<?php

namespace App\Domain\Model;

use DomainException;
use InvalidArgumentException;


class Task
{
    private string $id;
    private bool $isCompleted = false;
    public function __construct(
        private string $title,
        private string $description,
    )
    {
        $this->id = uniqid('', true);

        if(empty($title)) {
            throw new InvalidArgumentException('Title cannot be empty');
        }
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
