<?php

declare(strict_types=1);

namespace App\Domain\Student;

use App\Domain\Enrollment\Enrollment;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'students')]
class Student
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Column(type: 'string', unique: true)]
    private string $email;

    #[ORM\OneToMany(
        mappedBy: 'student',
        targetEntity: Enrollment::class,
        cascade: ['persist'],
        orphanRemoval: true
    )]
    private iterable $enrollments;

    public function __construct(StudentId $id, string $name, string $email)
    {
        if ($name === '') {
            throw new \InvalidArgumentException('Student name cannot be empty');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format');
        }

        $this->id = $id->value();
        $this->name = $name;
        $this->email = $email;
        $this->enrollments = [];
    }

    public function id(): StudentId
    {
        return new StudentId($this->id);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function enrollments(): iterable
    {
        return $this->enrollments;
    }

    public function addEnrollment(Enrollment $enrollment): void
    {
        foreach ($this->enrollments as $existingEnrollment) {
            if ($existingEnrollment->courseId()->equals($enrollment->courseId())) {
                throw new \DomainException('Student is already enrolled in this course');
            }
        }

        $this->enrollments[] = $enrollment;
    }

    public function updateProfile(string $name, string $email): void
    {
        if ($name === '') {
            throw new \InvalidArgumentException('Student name cannot be empty');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format');
        }

        $this->name = $name;
        $this->email = $email;
    }

    public function isEnrolledIn(string $courseId): bool
    {
        foreach ($this->enrollments as $enrollment) {
            if ($enrollment->courseId()->value() === $courseId) {
                return true;
            }
        }
        return false;
    }
}
