<?php

declare(strict_types=1);

namespace App\Domain\Subject;

use App\Domain\Course\Course;
use App\Domain\Teacher\Teacher;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'subjects')]
class Subject
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\ManyToOne(targetEntity: Course::class, inversedBy: 'subjects')]
    #[ORM\JoinColumn(nullable: false)]
    private Course $course;

    #[ORM\ManyToOne(targetEntity: Teacher::class, inversedBy: 'subjects')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Teacher $teacher = null;

    public function __construct(SubjectId $id, string $name, Course $course)
    {
        if ($name === '') {
            throw new \InvalidArgumentException('Subject name cannot be empty');
        }

        $this->id = $id->value();
        $this->name = $name;
        $this->course = $course;
    }

    public function id(): SubjectId
    {
        return new SubjectId($this->id);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function course(): Course
    {
        return $this->course;
    }

    public function courseId(): \App\Domain\Course\CourseId
    {
        return $this->course->id();
    }

    public function teacher(): ?Teacher
    {
        return $this->teacher;
    }

    public function assignTeacher(Teacher $teacher): void
    {
        // Regla de negoci: Un subject només pot tenir un teacher
        $this->teacher = $teacher;
    }

    public function removeTeacher(): void
    {
        $this->teacher = null;
    }

    public function hasTeacher(): bool
    {
        return $this->teacher !== null;
    }

    public function teacherId(): ?\App\Domain\Teacher\TeacherId
    {
        return $this->teacher?->id();
    }

    public function update(string $name, Course $course): void
    {
        if ($name === '') {
            throw new \InvalidArgumentException('Subject name cannot be empty');
        }

        $this->name = $name;
        $this->course = $course;
    }
}
