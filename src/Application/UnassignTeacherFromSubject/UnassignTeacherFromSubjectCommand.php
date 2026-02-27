<?php

declare(strict_types=1);

namespace App\Application\UnassignTeacherFromSubject;

final class UnassignTeacherFromSubjectCommand
{
    public function __construct(
        public readonly string $teacherId,
        public readonly string $subjectId
    ) {}
}
