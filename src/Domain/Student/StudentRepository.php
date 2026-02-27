<?php

declare(strict_types=1);

namespace App\Domain\Student;

interface StudentRepository
{
    public function find(StudentId $id): ?Student;
    public function findByEmail(string $email): ?Student;
    public function save(Student $student): void;
    public function delete(Student $student): void;
    public function findAll(): array;
}
