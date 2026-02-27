<?php

declare(strict_types=1);

namespace App\Domain\Enrollment;

final class EnrollmentId
{
    public function __construct(private string $value)
    {
        if ($value === '') {
            throw new \InvalidArgumentException('EnrollmentId cannot be empty');
        }
    }

    public static function generate(): self
    {
        return new self(
            uniqid('enrollment_', true)
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

    public function equals(EnrollmentId $other): bool
    {
        return $this->value === $other->value;
    }
}
