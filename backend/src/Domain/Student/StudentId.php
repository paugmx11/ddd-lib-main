<?php

declare(strict_types=1);

namespace App\Domain\Student;

final class StudentId
{
    public function __construct(private string $value)
    {
        if ($value === '') {
            throw new \InvalidArgumentException('StudentId cannot be empty');
        }
    }

    public static function generate(): self
    {
        return new self(
            uniqid('student_', true)
        );
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(StudentId $other): bool
    {
        return $this->value === $other->value;
    }
}
