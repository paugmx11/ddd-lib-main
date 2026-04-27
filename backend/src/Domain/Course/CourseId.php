<?php

declare(strict_types=1);

namespace App\Domain\Course;

final class CourseId
{
    public function __construct(private string $value)
    {
        if ($value === '') {
            throw new \InvalidArgumentException('CourseId cannot be empty');
        }
    }

    public static function generate(): self
    {
        return new self(
            uniqid('course_', true)
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

    public function equals(CourseId $other): bool
    {
        return $this->value === $other->value;
    }
}
