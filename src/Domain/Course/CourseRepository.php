<?php

declare(strict_types=1);

namespace App\Domain\Course;

interface CourseRepository
{
    public function find(CourseId $id): ?Course;
    public function findByName(string $name): ?Course;
    public function save(Course $course): void;
    public function delete(Course $course): void;
    public function findAll(): array;
    public function findActive(): array;
}
