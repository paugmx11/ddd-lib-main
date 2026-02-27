<?php

declare(strict_types=1);

namespace App\Domain\Enrollment;

interface EnrollmentRepository
{
    public function find(EnrollmentId $id): ?Enrollment;
    public function save(Enrollment $enrollment): void;
    public function delete(Enrollment $enrollment): void;
    public function findAll(): array;
    public function findByStudent(string $studentId): array;
    public function findByCourse(string $courseId): array;
    public function findByStudentAndCourse(string $studentId, string $courseId): ?Enrollment;
}
