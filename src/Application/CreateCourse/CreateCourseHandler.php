<?php

declare(strict_types=1);

namespace App\Application\CreateCourse;

use App\Domain\Course\Course;
use App\Domain\Course\CourseId;
use App\Domain\Course\CourseRepository;

final class CreateCourseHandler
{
    public function __construct(
        private CourseRepository $courseRepository
    ) {}

    public function handle(CreateCourseCommand $command): void
    {
        // Verificar que no existeix un curs amb el mateix nom
        $existingCourse = $this->courseRepository->findByName($command->name);
        if ($existingCourse !== null) {
            throw new \RuntimeException('A course with this name already exists');
        }

        $startDate = \DateTimeImmutable::createFromFormat('Y-m-d', $command->startDate);
        $endDate = \DateTimeImmutable::createFromFormat('Y-m-d', $command->endDate);

        if ($startDate === false || $endDate === false) {
            throw new \InvalidArgumentException('Invalid date format. Use Y-m-d');
        }

        $course = new Course(
            new CourseId($command->courseId),
            $command->name,
            $startDate,
            $endDate,
            $command->description
        );

        $this->courseRepository->save($course);
    }
}
