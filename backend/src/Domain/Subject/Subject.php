<?php

declare(strict_types=1);

namespace App\Domain\Subject;

use App\Domain\Course\Course;
use App\Domain\Teacher\Teacher;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * Multiple teachers can be assigned to the same subject.
     *
     * @var Collection<int, Teacher>
     */
    #[ORM\ManyToMany(targetEntity: Teacher::class, inversedBy: 'subjects')]
    #[ORM\JoinTable(name: 'subject_teachers')]
    #[ORM\JoinColumn(name: 'subject_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'teacher_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Collection $teachers;

    public function __construct(SubjectId $id, string $name, Course $course)
    {
        if ($name === '') {
            throw new \InvalidArgumentException('Subject name cannot be empty');
        }

        $this->id = $id->value();
        $this->name = $name;
        $this->course = $course;
        $this->teachers = new ArrayCollection();
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

    /**
     * @return Collection<int, Teacher>
     */
    public function teachers(): Collection
    {
        return $this->teachers;
    }

    public function assignTeacher(Teacher $teacher): void
    {
        if ($this->teachers->contains($teacher)) {
            return;
        }

        $this->teachers->add($teacher);
        $teacher->addSubject($this);
    }

    public function removeTeacher(Teacher $teacher): void
    {
        if (!$this->teachers->contains($teacher)) {
            return;
        }

        $this->teachers->removeElement($teacher);
        $teacher->removeSubject($this->id()->value());
    }

    public function hasTeacher(): bool
    {
        return !$this->teachers->isEmpty();
    }

    public function hasTeacherId(string $teacherId): bool
    {
        foreach ($this->teachers as $teacher) {
            if ($teacher->id()->value() === $teacherId) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<int, string>
     */
    public function teacherIds(): array
    {
        $ids = [];
        foreach ($this->teachers as $teacher) {
            $ids[] = $teacher->id()->value();
        }

        return $ids;
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
