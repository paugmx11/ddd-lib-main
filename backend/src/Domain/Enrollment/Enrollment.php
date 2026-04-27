<?php

declare(strict_types=1);

namespace App\Domain\Enrollment;

use App\Domain\Student\Student;
use App\Domain\Course\Course;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'enrollments')]
class Enrollment
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Student::class, inversedBy: 'enrollments')]
    #[ORM\JoinColumn(nullable: false)]
    private Student $student;

    #[ORM\ManyToOne(targetEntity: Course::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Course $course;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $enrolledAt;

    #[ORM\Column(type: 'string', length: 20)]
    private string $status; // 'active', 'completed', 'cancelled'

    public function __construct(
        EnrollmentId $id,
        Student $student,
        Course $course,
        \DateTimeImmutable $enrolledAt,
        string $status = 'active'
    ) {
        $this->id = $id->value();
        $this->student = $student;
        $this->course = $course;
        $this->enrolledAt = $enrolledAt;
        $this->status = $status;
    }

    public static function enroll(Student $student, Course $course): self
    {
        // Regla de negoci: No matricular si el curs ja ha acabat
        if ($course->hasEnded()) {
            throw new \DomainException('Cannot enroll in a course that has already ended');
        }

        // Regla de negoci: No matricular si el curs no està actiu
        if (!$course->isActive() && $course->hasStarted()) {
            throw new \DomainException('Cannot enroll in a course that is not currently active');
        }

        return new self(
            EnrollmentId::generate(),
            $student,
            $course,
            new \DateTimeImmutable(),
            'active'
        );
    }

    public function id(): EnrollmentId
    {
        return new EnrollmentId($this->id);
    }

    public function student(): Student
    {
        return $this->student;
    }

    public function studentId(): \App\Domain\Student\StudentId
    {
        return $this->student->id();
    }

    public function course(): Course
    {
        return $this->course;
    }

    public function courseId(): \App\Domain\Course\CourseId
    {
        return $this->course->id();
    }

    public function enrolledAt(): \DateTimeImmutable
    {
        return $this->enrolledAt;
    }

    public function status(): string
    {
        return $this->status;
    }

    public function complete(): void
    {
        $this->status = 'completed';
    }

    public function cancel(): void
    {
        $this->status = 'cancelled';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
