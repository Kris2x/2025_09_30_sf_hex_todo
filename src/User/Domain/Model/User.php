<?php

namespace App\User\Domain\Model;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User
{
    private const FIRST_NAME_MIN_LENGTH = 3;

    private const FIRST_NAME_MAX_LENGTH = 64;

    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    private string $id;

    #[ORM\Column(type: 'string', unique: true)]
    private string $email;

    #[ORM\Column(type: 'string')]
    private string $firstName;

    #[ORM\Column(type: 'string')]
    private string $lastName;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    public function __construct(
        string $email,
        string $firstName,
        string $lastName,

    )
    {
        $this->id = uniqid('', true);
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->createdAt = new DateTimeImmutable();
        $this->guard();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    private function guard(): void
    {
        if (strlen($this->firstName) < self::FIRST_NAME_MIN_LENGTH || strlen($this->firstName) > self::FIRST_NAME_MAX_LENGTH) {
            throw new InvalidArgumentException(sprintf('First name must be between %d and %d characters', self::FIRST_NAME_MIN_LENGTH, self::FIRST_NAME_MAX_LENGTH));
        }
    }
}
