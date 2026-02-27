<?php

declare(strict_types=1);

namespace App\Application\EnrollStudent;

final class EnrollStudentCommand
{
    public function __construct(
        public readonly string $studentId,
        public readonly string $courseId
    ) {}
}
