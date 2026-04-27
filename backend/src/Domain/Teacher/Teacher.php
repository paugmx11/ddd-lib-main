<?php

declare(strict_types=1);

namespace App\Domain\Teacher;

use App\Domain\Subject\Subject;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @var Collection<int, Subject>
     */
    #[ORM\ManyToMany(targetEntity: Subject::class, mappedBy: 'teachers')]
    private Collection $subjects;

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
        $this->subjects = new ArrayCollection();
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

    /**
     * @return Collection<int, Subject>
     */
    public function subjects(): Collection
    {
        return $this->subjects;
    }

    public function addSubject(Subject $subject): void
    {
        if ($this->subjects->contains($subject)) {
            return;
        }

        $this->subjects->add($subject);
    }

    public function removeSubject(string $subjectId): void
    {
        foreach ($this->subjects as $subject) {
            if ($subject->id()->value() === $subjectId) {
                $this->subjects->removeElement($subject);
                return;
            }
        }
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
