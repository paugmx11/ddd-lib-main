<?php

declare(strict_types=1);

namespace App\Application\CreateSubject;

final class CreateSubjectCommand
{
    public function __construct(
        public readonly string $subjectId,
        public readonly string $name,
        public readonly string $courseId
    ) {}
}
