<?php

declare(strict_types=1);

namespace App\Application\AssignTeacherToSubject;

final class AssignTeacherToSubjectCommand
{
    public function __construct(
        public readonly string $teacherId,
        public readonly string $subjectId
    ) {}
}
