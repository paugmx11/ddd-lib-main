<?php

declare(strict_types=1);

namespace App\Application\CreateCourse;

final class CreateCourseCommand
{
    public function __construct(
        public readonly string $courseId,
        public readonly string $name,
        public readonly string $startDate, // Format: Y-m-d
        public readonly string $endDate,   // Format: Y-m-d
        public readonly ?string $description = null
    ) {}
}
