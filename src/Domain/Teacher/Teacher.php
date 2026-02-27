<?php

declare(strict_types=1);

namespace App\Domain\Teacher;

use App\Domain\Subject\Subject;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'teachers')]
class Teacher
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Column(type: 'string', unique: true)]
    private string $email;

    #[ORM\OneToMany(
        mappedBy: 'teacher',
        targetEntity: Subject::class,
        cascade: ['persist']
    )]
    private iterable $subjects;

    public function __construct(TeacherId $id, string $name, string $email)
    {
        if ($name === '') {
            throw new \InvalidArgumentException('Teacher name cannot be empty');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format');
        }

        $this->id = $id->value();
        $this->name = $name;
        $this->email = $email;
        $this->subjects = [];
    }

    public function id(): TeacherId
    {
        return new TeacherId($this->id);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function subjects(): iterable
    {
        return $this->subjects;
    }

    public function addSubject(Subject $subject): void
    {
        $this->subjects[] = $subject;
    }

    public function removeSubject(string $subjectId): void
    {
        $this->subjects = array_values(array_filter(
            is_array($this->subjects) ? $this->subjects : iterator_to_array($this->subjects),
            static fn (Subject $subject): bool => $subject->id()->value() !== $subjectId
        ));
    }

    public function hasSubject(string $subjectId): bool
    {
        foreach ($this->subjects as $subject) {
            if ($subject->id()->value() === $subjectId) {
                return true;
            }
        }
        return false;
    }

    public function updateProfile(string $name, string $email): void
    {
        if ($name === '') {
            throw new \InvalidArgumentException('Teacher name cannot be empty');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format');
        }

        $this->name = $name;
        $this->email = $email;
    }
}
