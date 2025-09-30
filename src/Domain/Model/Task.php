<?php

namespace App\Domain\Model;

use Doctrine\ORM\Mapping as ORM;
use DomainException;
use InvalidArgumentException;

#[ORM\Entity]
#[ORM\Table(name: 'tasks')]
class Task
{
    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    private string $id;

    #[ORM\Column(type: 'boolean')]
    private bool $isCompleted = false;

    #[ORM\Column(type: 'string')]
    private string $title;

    #[ORM\Column(type: 'text')]
    private string $description = '';

    public function __construct(
        string $title,
        string $description = ''
    )
    {
        $this->id = uniqid('', true);
        if (empty($title)) {
            throw new InvalidArgumentException('Title cannot be empty');
        }

        $this->title = $title;
        $this->description = $description;
    }

    public function complete(): void
    {
        if ($this->isCompleted) {
            throw new DomainException('Task is already completed');
        }

        $this->isCompleted = true;
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
