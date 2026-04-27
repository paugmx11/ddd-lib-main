<?php

declare(strict_types=1);

namespace App\Application\CreateTeacher;

final class CreateTeacherCommand
{
    public function __construct(
        public readonly string $teacherId,
        public readonly string $name,
        public readonly string $email
    ) {}
}
