<?php

declare(strict_types=1);

namespace App\Domain\Course;

use App\Domain\Subject\Subject;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'courses')]
class Course
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description;

    #[ORM\Column(type: 'date_immutable')]
    private \DateTimeImmutable $startDate;

    #[ORM\Column(type: 'date_immutable')]
    private \DateTimeImmutable $endDate;

    #[ORM\OneToMany(
        mappedBy: 'course',
        targetEntity: Subject::class,
        cascade: ['persist'],
        orphanRemoval: true
    )]
    private iterable $subjects;

    public function __construct(
        CourseId $id,
        string $name,
        \DateTimeImmutable $startDate,
        \DateTimeImmutable $endDate,
        ?string $description = null
    ) {
        if ($name === '') {
            throw new \InvalidArgumentException('Course name cannot be empty');
        }

        if ($endDate <= $startDate) {
            throw new \InvalidArgumentException('End date must be after start date');
        }

        $this->id = $id->value();
        $this->name = $name;
        $this->description = $description;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->subjects = [];
    }

    public function id(): CourseId
    {
        return new CourseId($this->id);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function startDate(): \DateTimeImmutable
    {
        return $this->startDate;
    }

    public function endDate(): \DateTimeImmutable
    {
        return $this->endDate;
    }

    public function subjects(): iterable
    {
        return $this->subjects;
    }

    public function isActive(): bool
    {
        $now = new \DateTimeImmutable();
        return $now >= $this->startDate && $now <= $this->endDate;
    }

    public function hasStarted(): bool
    {
        $now = new \DateTimeImmutable();
        return $now >= $this->startDate;
    }

    public function hasEnded(): bool
    {
        $now = new \DateTimeImmutable();
        return $now > $this->endDate;
    }

    public function addSubject(Subject $subject): void
    {
        $this->subjects[] = $subject;
    }

    public function updateDetails(
        string $name,
        \DateTimeImmutable $startDate,
        \DateTimeImmutable $endDate,
        ?string $description = null
    ): void {
        if ($name === '') {
            throw new \InvalidArgumentException('Course name cannot be empty');
        }

        if ($endDate <= $startDate) {
            throw new \InvalidArgumentException('End date must be after start date');
        }

        $this->name = $name;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->description = $description;
    }
}
