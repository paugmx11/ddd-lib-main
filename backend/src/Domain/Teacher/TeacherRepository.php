<?php

declare(strict_types=1);

namespace App\Domain\Teacher;

interface TeacherRepository
{
    public function find(TeacherId $id): ?Teacher;
    public function findByEmail(string $email): ?Teacher;
    public function save(Teacher $teacher): void;
    public function delete(Teacher $teacher): void;
    public function findAll(): array;
}
