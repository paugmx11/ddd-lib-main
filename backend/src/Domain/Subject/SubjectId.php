<?php

declare(strict_types=1);

namespace App\Domain\Subject;

final class SubjectId
{
    public function __construct(private string $value)
    {
        if ($value === '') {
            throw new \InvalidArgumentException('SubjectId cannot be empty');
        }
    }

    public static function generate(): self
    {
        return new self(
            uniqid('subject_', true)
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

    public function equals(SubjectId $other): bool
    {
        return $this->value === $other->value;
    }
}
