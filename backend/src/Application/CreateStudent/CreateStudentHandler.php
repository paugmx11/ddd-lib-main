<?php

declare(strict_types=1);

namespace App\Application\CreateStudent;

use App\Domain\Student\Student;
use App\Domain\Student\StudentId;
use App\Domain\Student\StudentRepository;

final class CreateStudentHandler
{
    public function __construct(
        private StudentRepository $studentRepository
    ) {}

    public function handle(CreateStudentCommand $command): void
    {
        // Verificar que no existeix un student amb el mateix email
        $existingStudent = $this->studentRepository->findByEmail($command->email);
        if ($existingStudent !== null) {
            throw new \RuntimeException('A student with this email already exists');
        }

        $student = new Student(
            new StudentId($command->studentId),
            $command->name,
            $command->email
        );

        $this->studentRepository->save($student);
    }
}
