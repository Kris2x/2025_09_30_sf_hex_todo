<?php

namespace App\Domain\Model;

use DateTimeImmutable;
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

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $completedAt = null;

    #[ORM\Column(type: 'string')]
    private string $title;

    #[ORM\Column(type: 'text')]
    private string $description = '';

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

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
        $this->createdAt = new DateTimeImmutable();
    }

    public function complete(): void
    {
        if ($this->completedAt !== null) {
            throw new DomainException('Task is already completed');
        }

        $this->completedAt = new DateTimeImmutable();
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

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getCompletedAt(): ?DateTimeImmutable
    {
        return $this->completedAt;
    }
}
