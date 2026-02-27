<?php

declare(strict_types=1);

namespace App\Application\CreateStudent;

final class CreateStudentCommand
{
    public function __construct(
        public readonly string $studentId,
        public readonly string $name,
        public readonly string $email
    ) {}
}
