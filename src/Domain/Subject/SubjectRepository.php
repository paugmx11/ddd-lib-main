<?php

declare(strict_types=1);

namespace App\Domain\Subject;

interface SubjectRepository
{
    public function find(SubjectId $id): ?Subject;
    public function findByName(string $name): ?Subject;
    public function save(Subject $subject): void;
    public function delete(Subject $subject): void;
    public function findAll(): array;
    public function findByCourse(string $courseId): array;
    public function findByTeacher(string $teacherId): array;
}
