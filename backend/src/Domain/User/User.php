<?php

declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\Loan\Loan;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Column(type: 'string', unique: true)]
    private string $email;

    #[ORM\Column(type: 'string')]
    private string $passwordHash;

    #[ORM\OneToMany(
        mappedBy: 'user',
        targetEntity: Loan::class,
        cascade: ['persist'],
        orphanRemoval: true
    )]
    private iterable $loans;

    public function __construct(
        UserId $id,
        string $name,
        string $email,
        string $passwordHash
    ) {
        if ($name === '') {
            throw new \InvalidArgumentException('User name cannot be empty');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format');
        }

        if ($passwordHash === '') {
            throw new \InvalidArgumentException('Password hash cannot be empty');
        }

        $this->id = $id->value();
        $this->name = $name;
        $this->email = strtolower($email);
        $this->passwordHash = $passwordHash;
        $this->loans = [];
    }

    public function id(): UserId
    {
        return new UserId($this->id);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function passwordHash(): string
    {
        return $this->passwordHash;
    }

    public function addLoan(Loan $loan): void
    {
        $this->loans[] = $loan;
    }

    public function loans(): iterable
    {
        return $this->loans;
    }

    public function updateProfile(string $name, string $email): void
    {
        if ($name === '') {
            throw new \InvalidArgumentException('User name cannot be empty');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format');
        }

        $this->name = $name;
        $this->email = strtolower($email);
    }

    public function updatePassword(string $passwordHash): void
    {
        if ($passwordHash === '') {
            throw new \InvalidArgumentException('Password hash cannot be empty');
        }

        $this->passwordHash = $passwordHash;
    }

    public function verifyPassword(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->passwordHash);
    }
}
